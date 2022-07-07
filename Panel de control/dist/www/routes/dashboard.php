<?php

function draw_dashboard(){

  echo '
      <div class="container-fluid">
        <!-- Info boxes -->
        <div class="row">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <div class="card-tools">
                  <button type="button" class="btn btn-tool" data-card-widget="collapse">
                    <i class="fas fa-minus"></i>
                  </button>
                  <button type="button" class="btn btn-tool" data-card-widget="remove">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <!-- /.card-header -->

              <div class="card-body">';
		$total_ioc = $GLOBALS['dashboard']->countIoC();
		$total_quarantined = $GLOBALS['dashboard']->countQuarantinedIoC();
		$total_monitoring = $GLOBALS['dashboard']->countMonitoringIoC();
		$total_24h = $GLOBALS['dashboard']->countLast24hIoC();
		
echo '    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <!-- Small boxes (Stat box) -->
        <div class="row">
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
              <div class="inner">
                <h3>'.$total_ioc.'</h3>
                <p>Indicators of Compromise</p>
              </div>
              <div class="icon">
                <i class="fas fa-fingerprint"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
              <div class="inner">
                <h3>'.$total_quarantined.'</h3>
                <p>On quarantine</p>
              </div>
              <div class="icon">
                <i class="fas fa-lock"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
              <div class="inner">
                <h3>'.$total_monitoring.'</h3>
                <p>On monitoring</p>
              </div>
              <div class="icon">
                <i class="ion ion-search"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
          <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-success">
              <div class="inner">
                <h3>'.$total_24h.'</h3>
                <p>Last 24 hours</p>
              </div>
              <div class="icon">
                <i class="fas fa-clock"></i>
              </div>
            </div>
          </div>
          <!-- ./col -->
        </div>
	<div class="row">
	  <div class="col-md-6"> 
	      <!-- PIE CHART -->
              <div class="card card-danger">
                <div class="card-header">
                  <h3 class="card-title">'._("Total Indicator of Compromise by type").'</h3>
                  <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                      <i class="fas fa-minus"></i>
                    </button>
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                      <i class="fas fa-times"></i>
                    </button>
                  </div>
                </div>
                <div class="card-body">
                  <canvas id="pieChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                </div>
              <!-- /.card-body -->
              </div>	
           </div>
           <div class="col-md-6"> 
	      <!-- LINE CHART -->
	      <div class="card card-info">
	        <div class="card-header">
		  <h3 class="card-title">'._("Number of Reasons per day in the last month").'</h3>
		  <div class="card-tools">
		    <button type="button" class="btn btn-tool" data-card-widget="collapse">
		      <i class="fas fa-minus"></i>
		    </button>
		    <button type="button" class="btn btn-tool" data-card-widget="remove">
		      <i class="fas fa-times"></i>
		    </button>
		  </div>
		</div>
		<div class="card-body">
		  <div class="chart">
		    <canvas id="lineChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
		  </div>
		</div>
		<!-- /.card-body -->
	      </div>
          </div>
        </div>
      </div>
    </section>
    </div>
    <!-- ./card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.col -->
</div>
<!-- /.row -->

</div><!--/. container-fluid -->';
      
      $type_labels = $GLOBALS['ioc']->get_all_type_name();
      $type_values = $GLOBALS['dashboard']->countIoCTypes();
      $pie_labels = "[";
      $pie_values = "[";
      foreach ($type_labels as $i => $label)
      {
      	$pie_labels.="'".$label."',";
      	if(isset($type_values[$i])){
      		$pie_values.=$type_values[$i].",";
	} else{
		$pie_values.="0,";
	}
      }
      $pie_labels .="]";
      $pie_values .="]";
      

      $total_reasons = $GLOBALS['dashboard']->countReasonsLastMonth();
      $date_end = new DateTime();
      $date_end->modify('+1 day');
      $date_start = new DateTime();
      $date_start->modify('-30 day');
      
      $line_labels = "[";
      $line_values = "[";
      for($i=$date_start; $i <= $date_end; $i->modify('+1 day')){
         $temp_date = $i->format("Y-m-d");
      	 $line_labels.="'".$temp_date."', ";
         if(isset($total_reasons[$temp_date])){
      		$line_values.=$total_reasons[$temp_date].",";
	  } else{
      		$line_values.="0,";
	  }
      }
      $line_labels .= "]";
      $line_values .= "]";      
      

      echo "
	<script>
  	$(function () {
	    //-------------
	    //- DONUT CHART -
	    //-------------
	    // Get context with jQuery - using jQuery's .get() method.
	    var pieChartCanvas = $('#pieChart').get(0).getContext('2d')
	    var pieData        = {
	      labels: $pie_labels,
	      datasets: [
		{
		  data: $pie_values,	  
		  backgroundColor : ['#54478C', '#048BA8',  '#16DB93',  '#B9E769',  '#F1C453', '#E01E37', '#2C699A', '#0DB39E', '#83E377','#EFEA5A', '#F29E4C', ],  
		}
	      ]
	    }
	    var pieOptions     = {
	      maintainAspectRatio : false,
	      responsive : true,
	    }
	    //Create pie chart
	    new Chart(pieChartCanvas, {
	      type: 'pie',
	      data: pieData,
	      options: pieOptions
	    })";
	    
    echo "
	    var lineChartCanvas = $('#lineChart').get(0).getContext('2d')
	    var lineChartOptions = {
	      maintainAspectRatio : false,
	      responsive : true,
	      legend: {
		display: false
	      },
	      scales: {
		xAxes: [{
		  gridLines : {
		    display : false,
		  }
		}],
		yAxes: [{
		  gridLines : {
		    display : false,
		  }
		}]
	      }
	    }
	 
	    var lineChartData = {
	      labels  : $line_labels,
	      datasets: [
		{
		  label               : 'Indicators Detected',
		  backgroundColor     : 'rgba(60,141,188,0.9)',
		  borderColor         : 'rgba(60,141,188,0.8)',
		  pointRadius          : false,
		  pointColor          : '#3b8bba',
		  pointStrokeColor    : 'rgba(60,141,188,1)',
		  pointHighlightFill  : '#fff',
		  pointHighlightStroke: 'rgba(60,141,188,1)',
		  data                : $line_values
		}
	      ]
	    }
	    lineChartData.datasets[0].fill = false;
	    lineChartOptions.datasetFill = false
	 
	    var lineChart = new Chart(lineChartCanvas, {
	      type: 'line',
	      data: lineChartData,
	      options: lineChartOptions
	    })

		})
		</script>
	";
}

Route::add('/',function(){
  include('www/controllers/head.php');
  ?>
  <div class="content-wrapper">
    <?php include('www/controllers/content-header.php'); ?>
    <section class="content">
      <?php draw_dashboard(); ?>
    </section>
  </div>
  <?php
  include('www/controllers/footer.php');
},'get');

Route::add('/',function(){
  include('www/controllers/head.php');
  ?>
  <div class="content-wrapper">
    <?php include('www/controllers/content-header.php'); ?>
    <section class="content">
      <?php draw_dashboard(); ?>
    </section>
  </div>
  <?php
  include('www/controllers/footer.php');
},'post');
?>
