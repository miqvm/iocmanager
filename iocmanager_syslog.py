import sys
import traceback
import re
import json
from datetime import datetime,timedelta
import mysql.connector
import configparser
import socket
import argparse
import time
from hashlib import sha1

# Config file path
CONFIG_FILE_URL = "/opt/config.ini"


# Return value of json's property passed as parameter. It it don't exist or value is "" return 'None' so the bbdd insert won't crash
def get_property_value(prop,json):
	if(prop not in json or json[prop] == ""):
		return None
	else:
		return json[prop]		

# SYSLOG user code
FACILITY = {
    'kern': 0, 'user': 1, 'mail': 2, 'daemon': 3,
    'auth': 4, 'syslog': 5, 'lpr': 6, 'news': 7,
    'uucp': 8, 'cron': 9, 'authpriv': 10, 'ftp': 11,
    'local0': 16, 'local1': 17, 'local2': 18, 'local3': 19,
    'local4': 20, 'local5': 21, 'local6': 22, 'local7': 23,
}

LEVEL = {
    'emerg': 0, 'alert':1, 'crit': 2, 'err': 3,
    'warning': 4, 'notice': 5, 'info': 6, 'debug': 7
}

# https://gist.githubusercontent.com/haukurk/5ef80fa47ee60e815ce7/raw/2c450c65b9fde6d3431b14f1ddeffec2a63e7f1b/syslogger.py   
def Syslog(ip, parameters, msg, info_ioc, info_reason):

	# Send syslog UDP packet to given ip and port.
	port = int(parameters["Port"])
	# Split facility and level
	facility_level = parameters['Facility'].split('.');
	facility = FACILITY[facility_level[0]]
	level = LEVEL[facility_level[1]]
	# Overwrite message parameters with the IOC value
	msg = msg.format(ioc= info_ioc['name'], first_seen = info_ioc['first_seen'], last_seen = info_ioc['last_seen'], json_offence_level = info_ioc['json_offence_level'], type_name = info_ioc['type_name'], quarantine_end = info_ioc['quarantine_end'], monitoring_end = info_ioc['monitoring_end'], reason = info_reason['reason'], source = info_reason['source'], direction = info_reason['direction'], confidence = info_reason['confidence'], sin_malos_source = info_reason['sin_malos_source'], reason_date = info_reason['date'], share_level = info_reason['share_level'], disable = info_reason['disable'], disable_reason = info_reason['disable_reason'])	

	# Init socket
	sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
	# Data is the user code and the message
	data = ('<%d>%s' % (level + facility*8, msg)).encode()
	# Send the message through the socket
	sock.sendto(data, (ip, port))
	sock.close()
	

if __name__ == '__main__':
	try:
		# Read the Config.ini file
		config = configparser.ConfigParser()
		config.read(CONFIG_FILE_URL)

		# Connect to database
		mydb = mysql.connector.connect(
			host = config.get("database", "db_server"),
			user = config.get("database", "db_user"),
			password =config.get("database", "db_pass"),
			database = config.get("database", "db_database")
		)

		# Initializate cursor to operate with the DB
		mycursor = mydb.cursor()

		# Get all whitelisted IoC from the whitelist table
		sql = """SELECT name FROM whitelist"""
		mycursor.execute(sql)
		records = mycursor.fetchall()
		whitelist = []
		for row in records:
			whitelist.append(row[0])
		
		
		# Get all Output devices which output method is Syslog
		sql = """SELECT O.ip,O.parameters,O.message,OM.method_name FROM output O inner join output_method OM on O.id_method=OM.id_method;"""
		mycursor.execute(sql)
		records = mycursor.fetchall()
		info = []
		for row in records:
			info.append(row)
		
		# Transform last select to JSON objet following the schema: [{"ip": "X", "parameters": JSON(Y), "message": "Z", "method": "W"},]
		if len(info) > 0 :
			output_devices_raw = "["
			for i in info:
				output_devices_raw += '{"ip": "'+i[0]+'", "parameters": ' + i[1] + ', "message": "\'' + i[2] + '\'", "method": "' + i[3] +'"},'
			output_devices_raw = output_devices_raw[:-1] + "]"
			output_devices = json.loads(output_devices_raw)
		else:
			output_devices = {}

		# Get JSON Offence Level quarantine time policy stored on config.ini
		json_offence_level_data = config.get("json_offence_level", "json_offence_level")
		raw_json = re.search('{(.*)}',json_offence_level_data).group(0)
		json_offence_level_data = json.loads(raw_json)

		# Get Share Level TLP translation stored on config.ini
		share_level_data = config.get("share_level", "share_level")
		raw_share_level = re.search('{(.*)}',share_level_data).group(0)
		share_level_data = json.loads(raw_share_level)
		
		# Get IoC type name to Type ID translation stored on config.ini
		ioc_type_data = config.get("ioc_type", "ioc_type")
		raw_type = re.search('{(.*)}',ioc_type_data).group(0)
		ioc_type_data = json.loads(raw_type)
		
		# Get Last Seen Margin time stored on config.ini, also set flag to 0 to avoid message duplication
		time_lastseen_margin = int(config.get("last_seen_margin", "last_seen_margin"))
		flag_msg_duplicated = 0
		
		# Loop will read new logged messages and record them on the database. Also send data to the output devices
		while True:
			try:
				# Get message from input file
				line = sys.stdin.readline()
				msg = line.strip()


				# If msg is empty, then don't process it
				if msg in (None,''):
					now_datetime = datetime.now()
					print(now_datetime.strftime("%Y-%m-%d %H:%M:%S"),"Ignore, because line is empty.",flush=True)
				else:
					# Sanitize message and get only the content of the Syslog msg
					# Syslog msg (STRING) -> data(JSON)
					raw = re.search('{(.*)}',msg).group(0)
					data = json.loads(raw)
					
					# If the received msg hasn't got the following properties it won't be processed and a message error will be written on the output file
					if get_property_value("ioc",data) is None:
						timestamp = msg.split(' ')[0]
						print(timestamp, "ERROR: NO 'IOC' PROVIDED ON MESSAGE [", data,"]",flush=True)
					elif get_property_value("type",data) is None:
						timestamp = msg.split(' ')[0]
						print(timestamp, "ERROR: NO 'TYPE' PROVIDED ON MESSAGE [", data,"]",flush=True)
					elif get_property_value("source",data) is None:
						timestamp = msg.split(' ')[0]
						print(timestamp, "ERROR: NO 'SOURCE' PROVIDED ON MESSAGE [", data,"]",flush=True)
					elif get_property_value("reason",data) is None:
						timestamp = msg.split(' ')[0]
						print(timestamp, "ERROR: NO 'REASON' PROVIDED ON MESSAGE [", data,"]",flush=True)
					else:
						# Init the IOC and REASON info
						info_ioc = {}
						info_reason = {}
						
						# Get IoC name from the received msg
						info_ioc['name'] = data["ioc"]
						
						# Check if the IoC is not a whitelisted IoC 
						if not info_ioc['name'] in whitelist:
							# Get current datetime to reduce calls to said function and an homogenous current datetime on inserts
							now_datetime = datetime.now()

							# Check if IoC is already on the database, get ID and first_seen, last_seen, type_name, quarantine_end and monitoring_end for later purpose
							sql = """SELECT id_ioc, first_seen, last_seen, T.type_name, json_offence_level, quarantine_end, monitoring_end FROM ioc I INNER JOIN type T ON I.type_id = T.id_type WHERE name_ioc=%s OR url = %s"""
							mycursor.execute(sql, (info_ioc['name'],info_ioc['name']))
							records = mycursor.fetchone()

							# If it's a new IoC, insert it on database
							if records is None:
								# Apply JSON Offence Level policy
								info_ioc['json_offence_level'] = get_property_value("json_offence_level", data["others"])
								info_ioc['quarantine_end'] = get_property_value("quarantine_end", data["others"])
								info_ioc['monitoring_end'] = get_property_value("monitoring_end", data["others"])
							
								# Set '1' as Default JSON Offence Level
								if(info_ioc['json_offence_level'] is None):
									info_ioc['json_offence_level'] = '1'
								
								# If there is not custom quarantine or monitoring end time apply JSON Offence Level Policy
								if(info_ioc['quarantine_end'] is None):
									quarantine_time = json_offence_level_data[info_ioc['json_offence_level']]["quarantine_time"]
									new_time = now_datetime + timedelta(hours=quarantine_time)
									info_ioc['quarantine_end'] = new_time.strftime("%Y-%m-%d %H:%M:%S")
								
								if(info_ioc['monitoring_end'] is None):
									monitoring_time = json_offence_level_data[info_ioc['json_offence_level']]["monitoring_time"]
									new_time = now_datetime + timedelta(hours=monitoring_time)
									info_ioc['monitoring_end'] = new_time.strftime("%Y-%m-%d %H:%M:%S")
								
								# Quarantine ending date must be equal or sooner than monitoring ending date
								if(datetime.strptime(info_ioc['quarantine_end'], '%Y-%m-%d %H:%M:%S') > datetime.strptime(info_ioc['monitoring_end'], '%Y-%m-%d %H:%M:%S')):
									info_ioc['monitoring_end']=info_ioc['quarantine_end']

								# Translate Type name to 'id_type'
								if(data["type"]=="ip"):
									if(":" in data["ioc"]):
										info_ioc['type'] = ioc_type_data["ipv6"] 
										info_ioc['type_name'] = "ipv6"
									else:
										info_ioc['type'] = ioc_type_data["ipv4"] 
										info_ioc['type_name'] = "ipv4"
								else:
									info_ioc['type'] = ioc_type_data[data["type"]] 
									info_ioc['type_name'] = data["type"]
								
								# Insert new IoC
								sql_ioc = """INSERT INTO ioc (name_ioc, first_seen, last_seen, json_offence_level, url, type_id, quarantine_end, monitoring_end) VALUES (%s,%s,%s,%s,%s,%s,%s,%s)"""

								# Get attribute values
								info_ioc['first_seen'] = now_datetime.strftime("%Y-%m-%d %H:%M:%S")
								info_ioc['last_seen'] = now_datetime.strftime("%Y-%m-%d %H:%M:%S")

								# If Type is URL or DOMAIN change the ioc_name to a hashed value (sha1) of the URL/Domain
								if(info_ioc['type_name'] == "url" or info_ioc['type_name'] == "domain"):
									val = (sha1(info_ioc['name'].encode()).hexdigest(), info_ioc['first_seen'], info_ioc['last_seen'], info_ioc['json_offence_level'], info_ioc['name'], info_ioc['type'], info_ioc['quarantine_end'], info_ioc['monitoring_end'],)
								else:
									val = (info_ioc['name'], info_ioc['first_seen'], info_ioc['last_seen'], info_ioc['json_offence_level'], None, info_ioc['type'], info_ioc['quarantine_end'], info_ioc['monitoring_end'],)
								
								# Insert new IoC in the database
								mycursor.execute(sql_ioc, val)
								mydb.commit()

								# Get ID of the last inserted IoC
								sql = "SELECT LAST_INSERT_ID()"
								mycursor.execute(sql)
								id_ioc = mycursor.fetchone()[0]
								
							# If it already exists on the database
							else:
								# Check if it hasn't been added recently and if it's not, change last_seen date		
								id_ioc = records[0]
								info_ioc['first_seen'] = records[1].strftime("%Y-%m-%d %H:%M:%S")
								info_ioc['last_seen'] = records[2]
								info_ioc['type_name'] = records[3]
								info_ioc['json_offence_level'] = records[4]
								info_ioc['quarantine_end'] = records[5]
								info_ioc['monitoring_end'] = records[6]
								
								
								# Date to determine if it could be a duplicated msg
								last_seen_margin = now_datetime - timedelta(seconds=time_lastseen_margin)

								# Duplicated message
								if(info_ioc['last_seen'] > last_seen_margin):
									flag_msg_duplicated = 1
									print(now_datetime.strftime("%Y-%m-%d %H:%M:%S"),"Rejected IoC because of time_lastseen_margin: ", data,flush=True)

								# Not duplicated
								else:
									# IoC is not on quarantine
									if(now_datetime > info_ioc['quarantine_end']):
										# If its still monitoring increment JSON Offence Level
										if(now_datetime<info_ioc['monitoring_end']): 
											# Dont increment if its already the maximum value
											if(info_ioc['json_offence_level'] < len(json_offence_level_data)-1):
												info_ioc['json_offence_level'] = info_ioc['json_offence_level']+1
										
										# Update quarantine_end and monitoring_end applying JSON Offence Level policy
										quarantine_time = json_offence_level_data[str(info_ioc['json_offence_level'])]["quarantine_time"]
										new_time = now_datetime + timedelta(hours=quarantine_time)
										info_ioc['quarantine_end'] = new_time.strftime("%Y-%m-%d %H:%M:%S")
										
										monitoring_time = json_offence_level_data[str(info_ioc['json_offence_level'])]["monitoring_time"]
										new_time = now_datetime + timedelta(hours=monitoring_time)
										info_ioc['monitoring_end'] = new_time.strftime("%Y-%m-%d %H:%M:%S")
									
									# Update values
									sql = """UPDATE ioc SET last_seen=%s, json_offence_level=%s,quarantine_end=%s,monitoring_end=%s WHERE id_ioc=%s"""
									info_ioc['last_seen']= now_datetime.strftime("%Y-%m-%d %H:%M:%S")
									val = (info_ioc['last_seen'], info_ioc['json_offence_level'],info_ioc['quarantine_end'],info_ioc['monitoring_end'],id_ioc,)
									mycursor.execute(sql, val)
									mydb.commit()


							# If msg is not a duplicated one, insert a reason and notice it to the output device
							if not flag_msg_duplicated:

								# Set the Reason attribute, if them are None it will keep as NULL value. Share level has RED value has default
								info_reason['direction'] = get_property_value("direction", data["others"])
								info_reason['confidence'] = get_property_value("confidence", data["others"])
								info_reason['sin_malos_source'] = get_property_value("sin_malos_source", data["others"])
								sl = get_property_value("share_level", data["others"])	
								if(sl is not None):
									info_reason['share_level']  = sl
									info_reason['share_level_id'] = share_level_data[sl]["index"]

								else: 
									info_reason['share_level'] = "RED"
									info_reason['share_level_id'] = share_level_data[info_reason['share_level']]["index"]

								info_reason['disable'] = get_property_value("disable", data["others"])
								info_reason['disable_reason'] = get_property_value("disable_reason", data["others"])
								
								info_reason['date'] = now_datetime.strftime("%Y-%m-%d %H:%M:%S")
								info_reason['reason'] = data["reason"]
								info_reason['source'] = data["source"] 

								# Insert REASON on the database
								sql = """INSERT INTO reason (reason,source,direction,confidence,sin_malos_source,date,share_level,disable,disable_reason,ioc_id) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)"""
								val = (info_reason['reason'], info_reason['source'],info_reason['direction'],info_reason['confidence'],info_reason['sin_malos_source'], info_reason['date'], info_reason['share_level_id'],info_reason['disable'],info_reason['disable_reason'],id_ioc)
								
								mycursor.execute(sql, val)
								mydb.commit()

								# Send the IOC info to the output devices
								for device in output_devices:
									eval(device["method"])(device["ip"], device["parameters"],device["message"],info_ioc, info_reason)

							
							flag_msg_duplicated = 0
				
			except Exception as e:
				err = traceback.format_exc()
				print("MSG:",msg,err, flush=True) 

	except Exception as e:
		err = traceback.format_exc()
		print(err, flush=True)
		# Wait 30 seconds before exiting
		time.sleep(30)

