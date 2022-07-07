#!/usr/bin/python3

import json
import requests
import ipaddress
import os
import time
import configparser
import mysql.connector

#"""""""""""""""""""""""""""""""""""""""""""""""""
# PARAMETERS
#"""""""""""""""""""""""""""""""""""""""""""""""""
CONFIG_FILE_URL = "/opt/config.ini"

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

base_url = 'substituir per SELECT ioc i confidence + alt dels seus reasons'
percentage_b = float(config.get("ipv4_optimizer", "percentage_b"))
percentage_c = float(config.get("ipv4_optimizer", "percentage_c"))
percentage_d = float(config.get("ipv4_optimizer", "percentage_d"))
min_to_process = int(config.get("ipv4_optimizer", "min_to_process"))
max_to_edl = int(config.get("ipv4_optimizer", "max_to_edl"))
max_to_dag = int(config.get("ipv4_optimizer", "max_to_dag"))
edl_output_file = config.get("ipv4_optimizer", "edl_output_file")
edl_output_file = edl_output_file[1: -1]
dag_output_file = config.get("ipv4_optimizer", "dag_output_file")
dag_output_file = dag_output_file[1: -1]
overflow_output_file = config.get("ipv4_optimizer", "overflow_output_file")
overflow_output_file = overflow_output_file[1: -1]
stats_output_file = config.get("ipv4_optimizer", "stats_output_file")
stats_output_file = stats_output_file[1: -1]
#"""""""""""""""""""""""""""""""""""""""""""""""""
edlIndicators = 0
dagIndicators = 0

def printIndicatorToFile(indicator):
    global edlIndicators
    global dagIndicators
    global max_to_edl
    global max_to_dag
    global file_edl
    global file_dag
    global file_overflow

    if(edlIndicators < max_to_edl):
        file_edl.write(indicator+"\n")
        edlIndicators+=1
    elif(dagIndicators < max_to_dag):
        file_dag.write(indicator+"\n")
        dagIndicators+=1
    else:
        file_overflow.write(indicator+"\n")


class IPTree(object):
    "IP tree node."

    def __init__(self):
        self.ipv4 = {}

    def add_ip(self, a, mask=32, confidence=100):

        num_ips = 2 ** (32 - mask)

        byte1 = int(str(a).split(".")[0])
        byte2 = int(str(a).split(".")[1])
        byte3bin = bin(int(str(a).split(".")[2])+256)[3:]
        byte3binsplit = [byte3bin[i] for i in range (0, len(byte3bin))]
        byte4bin = bin(int(str(a).split(".")[3])+256)[3:]
        byte4binsplit = [byte4bin[i] for i in range (0, len(byte4bin))]

        if (not byte1 in self.ipv4):
            self.ipv4[byte1] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['ips'] += num_ips

        if (not byte2 in self.ipv4[byte1]['a']):
            self.ipv4[byte1]['a'][byte2] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['ips'] += num_ips

        if(mask <= 16):
            return
        #BYTE3
        byte31 = byte3binsplit[0]
        byte32 = byte3binsplit[1]
        byte33 = byte3binsplit[2]
        byte34 = byte3binsplit[3]
        byte35 = byte3binsplit[4]
        byte36 = byte3binsplit[5]
        byte37 = byte3binsplit[6]
        byte38 = byte3binsplit[7]

        if (not byte3binsplit[0] in self.ipv4[byte1]['a'][byte2]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['ips'] += num_ips
        if(mask == 17):
            return
        if (not byte3binsplit[1] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['ips'] += num_ips
        if(mask == 18):
            return
        if (not byte3binsplit[2] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['ips'] += num_ips
        if(mask == 19):
            return
        if (not byte3binsplit[3] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['ips'] += num_ips
        if(mask == 20):
            return
        if (not byte3binsplit[4] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['ips'] += num_ips
        if(mask == 21):
            return
        if (not byte3binsplit[5] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['ips'] += num_ips
        if(mask == 22):
            return
        if (not byte3binsplit[6] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['ips'] += num_ips
        if(mask == 23):
            return
        if (not byte3binsplit[7] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['ips'] += num_ips
        if(mask == 24):
            return
        #BYTE4
        byte41 = byte4binsplit[0]
        byte42 = byte4binsplit[1]
        byte43 = byte4binsplit[2]
        byte44 = byte4binsplit[3]
        byte45 = byte4binsplit[4]
        byte46 = byte4binsplit[5]
        byte47 = byte4binsplit[6]
        byte48 = byte4binsplit[7]

        if (not byte4binsplit[0] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['ips'] += num_ips
        if(mask == 25):
            return
        if (not byte4binsplit[1] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['ips'] += num_ips
        if(mask == 26):
            return
        if (not byte4binsplit[2] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['ips'] += num_ips
        if(mask == 27):
            return
        if (not byte4binsplit[3] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['ips'] += num_ips
        if(mask == 28):
            return
        if (not byte4binsplit[4] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a'][byte45] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a'][byte45]['ips'] += num_ips
        if(mask == 29):
            return
        if (not byte4binsplit[5] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a'][byte45]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a'][byte45]['a'][byte46] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a'][byte45]['a'][byte46]['ips'] += num_ips
        if(mask == 30):
            return
        if (not byte4binsplit[6] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a'][byte45]['a'][byte46]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a'][byte45]['a'][byte46]['a'][byte47] = {'ips':num_ips, 'a':{}}
        else:
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a'][byte45]['a'][byte46]['a'][byte47]['ips'] += num_ips
        if(mask == 31):
            return
        if (not byte4binsplit[7] in self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a'][byte45]['a'][byte46]['a'][byte47]['a']):
            self.ipv4[byte1]['a'][byte2]['a'][byte31]['a'][byte32]['a'][byte33]['a'][byte34]['a'][byte35]['a'][byte36]['a'][byte37]['a'][byte38]['a'][byte41]['a'][byte42]['a'][byte43]['a'][byte44]['a'][byte45]['a'][byte46]['a'][byte47]['a'][byte48] = {"ip":str(a),"confidence":confidence}

#"""""""""""""""""""""""""""""""""""""""""""""""""
# FETCH FROM PA
#"""""""""""""""""""""""""""""""""""""""""""""""""
startTime = time.time()

# Get all whitelisted IoC from the whitelist table
sql = """SELECT name_ioc, MAX(confidence) FROM ioc I INNER JOIN reason R ON I.id_ioc=R.ioc_id WHERE I.type_id=1 AND quarantine_end>NOW() GROUP BY R.ioc_id;"""
mycursor.execute(sql)
records = mycursor.fetchall()
resp = ""
for row in records:
	
	resp+=row[0]+","+str(row[1])+"\n"

indicatorsList = resp
indicatorsListArray = indicatorsList.splitlines()
endPAFetchTime = time.time()
#"""""""""""""""""""""""""""""""""""""""""""""""""
if(len(indicatorsListArray) < min_to_process):
    # Don't process because there are not enough indicators.
    file_stats = open(stats_output_file,"a")
    #time, fetch_from_source(segs), process (segs), optimize (segs), original indicators, final indicators, percentage optimized, cidr, ipAlone, edlIndicators, dagIndicators, overflowIndicators, cidr(1), cidr(2),... , cidr(31), JSON(confidence)
    file_stats.write("%s,%.2f,%.2f,%.2f,%s,'NotEnoughIndicators'\n" %(time.ctime(),(endPAFetchTime - startTime),0,0,len(indicatorsListArray) ))
    file_stats.close()
else:
    # Start to process
    ipv4 = IPTree()
    finalIndicators=0
    ipAlone=0
    totalIPBlocked=0
    ipAloneConfidence={}
    maskIndicatorsByCIDR ={}
    for x in range(1,32):
        maskIndicatorsByCIDR[x]=0

    file_edl = open(edl_output_file+".tmp","w")
    file_dag = open(dag_output_file+".tmp","w")
    file_overflow = open(overflow_output_file+".tmp","w")

    #"""""""""""""""""""""""""""""""""""""""""""""""""
    # PROCESS
    #"""""""""""""""""""""""""""""""""""""""""""""""""
    for indicator in indicatorsListArray:
        indicatorInfo = str(indicator).split(',')

        if (indicatorInfo[0].find('/') != -1):
            #RANG
            net = indicatorInfo[0].split('/')
            if(int(net[1]) < 16):
                if (not int(net[1]) in maskIndicatorsByCIDR):
                    maskIndicatorsByCIDR[int(net[1])] = 1
                else:
                    maskIndicatorsByCIDR[int(net[1])] += 1
                printIndicatorToFile(indicatorInfo[0])
                finalIndicators+=1
                totalIPBlocked+= 2 ** (32 - int(net[1]))
            else:
                ipv4.add_ip(net[0],int(net[1]),int(indicatorInfo[1]))
                totalIPBlocked+= 2 ** (32 - int(net[1]))
        else:
            #IP
            ipv4.add_ip(indicatorInfo[0],32,int(indicatorInfo[1]))
            totalIPBlocked+=1

    endProcesTime = time.time()
    #"""""""""""""""""""""""""""""""""""""""""""""""""


    #"""""""""""""""""""""""""""""""""""""""""""""""""
    # OPTIMIZE_PROCESS
    #"""""""""""""""""""""""""""""""""""""""""""""""""
    for octet1 in sorted(ipv4.ipv4):
        #we don't optimize first byte (we understand there won't be less than /8 networks.).
        for octet2 in sorted(ipv4.ipv4[octet1]['a']):
            #we don't optimize second byte (we understand there won't be less than /16 networks.).
            if(float(ipv4.ipv4[octet1]['a'][octet2]['ips']) > float(65536)*percentage_b):
                printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.0.0/16')
                maskIndicatorsByCIDR[16] += 1
                finalIndicators+=1
            else:
                for octet31 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a']):
                    #If there are more than % of 32768 possible IP addresses, then group.
                    if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['ips']) > float(32768)*percentage_c):
                        printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+'0000000',2))+'.0/17')
                        maskIndicatorsByCIDR[17] += 1
                        finalIndicators+=1
                    else:
                        for octet32 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a']):
                            #If there are more than % of 16384 possible IP addresses, then group.
                            if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['ips']) > float(16384)*percentage_c):
                                printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+'000000',2))+'.0/18')
                                maskIndicatorsByCIDR[18] += 1
                                finalIndicators+=1
                            else:
                                for octet33 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a']):
                                    #If there are more than % of 8192 possible IP addresses, then group.
                                    if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['ips']) > float(8192)*percentage_c):
                                        printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+'00000',2))+'.0/19')
                                        maskIndicatorsByCIDR[19] += 1
                                        finalIndicators+=1
                                    else:
                                        for octet34 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a']):
                                            #If there are more than % of 4096 possible IP addresses, then group.
                                            if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['ips']) > float(4096)*percentage_c):
                                                printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+'0000',2))+'.0/20')
                                                maskIndicatorsByCIDR[20] += 1
                                                finalIndicators+=1
                                            else:
                                                for octet35 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a']):
                                                    #If there are more than % of 2048 possible IP addresses, then group.
                                                    if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['ips']) > float(2048)*percentage_c):
                                                        printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+'000',2))+'.0/21')
                                                        maskIndicatorsByCIDR[21] += 1
                                                        finalIndicators+=1
                                                    else:
                                                        for octet36 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a']):
                                                            #If there are more than % of 1024 possible IP addresses, then group.
                                                            if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['ips']) > float(1024)*percentage_c):
                                                                printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+'00',2))+'.0/22')
                                                                maskIndicatorsByCIDR[22] += 1
                                                                finalIndicators+=1
                                                            else:
                                                                for octet37 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a']):
                                                                    #If there are more than % of 512 possible IP addresses, then group.
                                                                    if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['ips']) > float(512)*percentage_c):
                                                                        printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+octet37+'0',2))+'.0/23')
                                                                        maskIndicatorsByCIDR[23] += 1
                                                                        finalIndicators+=1
                                                                    else:
                                                                        for octet38 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a']):
                                                                            #If there are more than % of 256 possible IP addresses, then group.
                                                                            if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['ips']) > float(256)*percentage_d):
                                                                                printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+octet37+octet38,2))+'.0/24')
                                                                                maskIndicatorsByCIDR[24] += 1
                                                                                finalIndicators+=1
                                                                            else:
                                                                                for octet41 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a']):
                                                                                    #If there are more than % of 128 possible IP addresses, then group.
                                                                                    if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['ips']) > float(128)*percentage_d):
                                                                                        printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+octet37+octet38,2))+'.'+str(int(octet41+'0000000',2))+'/25')
                                                                                        maskIndicatorsByCIDR[25] += 1
                                                                                        finalIndicators+=1
                                                                                    else:
                                                                                        for octet42 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a']):
                                                                                            #If there are more than % of 64 possible IP addresses, then group.
                                                                                            if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['ips']) > float(64)*percentage_d):
                                                                                                printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+octet37+octet38,2))+'.'+str(int(octet41+octet42+'000000',2))+'/26')
                                                                                                maskIndicatorsByCIDR[26] += 1
                                                                                                finalIndicators+=1
                                                                                            else:
                                                                                                for octet43 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a']):
                                                                                                    #If there are more than % of 32 possible IP addresses, then group.
                                                                                                    if(float(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['ips']) > float(32)*percentage_d):
                                                                                                        printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+octet37+octet38,2))+'.'+str(int(octet41+octet42+octet43+'00000',2))+'/27')
                                                                                                        maskIndicatorsByCIDR[27] += 1
                                                                                                        finalIndicators+=1
                                                                                                    else:
                                                                                                        for octet44 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a']):
                                                                                                            #If there are more than % of 16 possible IP addresses, then group.
                                                                                                            if(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['ips'] > 4):
                                                                                                                printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+octet37+octet38,2))+'.'+str(int(octet41+octet42+octet43+octet44+'0000',2))+'/28')
                                                                                                                maskIndicatorsByCIDR[28] += 1
                                                                                                                finalIndicators+=1
                                                                                                            else:
                                                                                                                for octet45 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a']):
                                                                                                                    #If there are more than % of 8 possible IP addresses, then group.
                                                                                                                    if(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['ips'] > 3):
                                                                                                                        printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+octet37+octet38,2))+'.'+str(int(octet41+octet42+octet43+octet44+octet45+'000',2))+'/29')
                                                                                                                        maskIndicatorsByCIDR[29] += 1
                                                                                                                        finalIndicators+=1
                                                                                                                    else:
                                                                                                                        for octet46 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a']):
                                                                                                                            #If there are more than % of 4 possible IP addresses, then group.
                                                                                                                            if(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a'][octet46]['ips'] > 2):
                                                                                                                                printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+octet37+octet38,2))+'.'+str(int(octet41+octet42+octet43+octet44+octet45+octet46+'00',2))+'/30')
                                                                                                                                maskIndicatorsByCIDR[30] += 1
                                                                                                                                finalIndicators+=1
                                                                                                                            else:
                                                                                                                                for octet47 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a'][octet46]['a']):
                                                                                                                                    #If there are more than % of 2 possible IP addresses, then group.
                                                                                                                                    if(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a'][octet46]['a'][octet47]['ips'] > 1):
                                                                                                                                        printIndicatorToFile(str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+octet37+octet38,2))+'.'+str(int(octet41+octet42+octet43+octet44+octet45+octet46+octet47+'0',2))+'/31')
                                                                                                                                        maskIndicatorsByCIDR[31] += 1
                                                                                                                                        finalIndicators+=1
                                                                                                                                    else:
                                                                                                                                        for octet48 in sorted(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a'][octet46]['a'][octet47]['a']):
                                                                                                                                            #print (ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a'][octet46]['a'][octet47]['a'][octet48])
                                                                                                                                            # print (str(octet1)+'.'+str(octet2)+'.'+str(int(octet31+octet32+octet33+octet34+octet35+octet36+octet37+octet38,2))+'.'+str(int(octet41+octet42+octet43+octet44+octet45+octet46+octet47+octet48,2))+'--->CONF:'+)
                                                                                                                                            finalIndicators+=1
                                                                                                                                            ipAlone+=1
                                                                                                                                            if (not ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a'][octet46]['a'][octet47]['a'][octet48]['confidence'] in ipAloneConfidence):
                                                                                                                                                ipAloneConfidence[ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a'][octet46]['a'][octet47]['a'][octet48]['confidence']] = [ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a'][octet46]['a'][octet47]['a'][octet48]['ip']]
                                                                                                                                            else:
                                                                                                                                                ipAloneConfidence[ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a'][octet46]['a'][octet47]['a'][octet48]['confidence']].append(ipv4.ipv4[octet1]['a'][octet2]['a'][octet31]['a'][octet32]['a'][octet33]['a'][octet34]['a'][octet35]['a'][octet36]['a'][octet37]['a'][octet38]['a'][octet41]['a'][octet42]['a'][octet43]['a'][octet44]['a'][octet45]['a'][octet46]['a'][octet47]['a'][octet48]['ip'])

    #"""""""""""""""""""""""""""""""""""""""""""""""""
    file_stats = open(stats_output_file,"a")
    str_confidence=''
    for ipAConf in sorted(ipAloneConfidence, reverse=True):
        str_confidence += ',"%s":"%s"' %(ipAConf, len(ipAloneConfidence[ipAConf]))
        for ip in ipAloneConfidence[ipAConf]:
            printIndicatorToFile(ip)

    #time, fetch_from_source(segs), process (segs), optimize (segs), original indicators, final indicators, percentage optimized, cidr, ipAlone, totalIPBlocked, edlIndicators, dagIndicators, overflowIndicators, cidr(1), cidr(2),... , cidr(31), JSON(confidence)
    file_stats.write("%s,%.2f,%.2f,%.2f,%s,%s,%.2f,%s,%s,%s,%s,%s,%s" %(time.ctime(),(endPAFetchTime - startTime),(endProcesTime - endPAFetchTime),(time.time() - endProcesTime),len(indicatorsListArray),finalIndicators,(100.0 * (1 -(float(finalIndicators) / float(len(indicatorsListArray))))),(finalIndicators - ipAlone),ipAlone,totalIPBlocked,edlIndicators,dagIndicators,(finalIndicators-(edlIndicators+dagIndicators)) ))
    for cidr in maskIndicatorsByCIDR:
        file_stats.write (",%s" %(maskIndicatorsByCIDR[cidr]))

    file_stats.write (",\'{%s}\'\n" %(str_confidence.lstrip(',')))

    file_edl.close()
    file_dag.close()
    file_overflow.close()
    file_stats.close()

    os.rename(edl_output_file+'.tmp', edl_output_file)
    os.rename(dag_output_file+'.tmp', dag_output_file)
    os.rename(overflow_output_file+'.tmp', overflow_output_file)

