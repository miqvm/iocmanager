      <!-- Control Sidebar -->
      <aside class="control-sidebar control-sidebar-dark">
        <!-- Control sidebar content goes here -->
      </aside>
      <!-- /.control-sidebar -->

      <!-- Main Footer -->
      <footer class="main-footer">
        <div class="float-right d-none d-sm-inline-block">
		IoC Manager
        </div>
      </footer>
    </div>
    <!-- ./wrapper -->

<!-- Bootstrap 4 -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- Select2 -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/select2/js/select2.full.min.js"></script>
<!-- Bootstrap4 Duallistbox -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/bootstrap4-duallistbox/jquery.bootstrap-duallistbox.min.js"></script>
<!-- InputMask -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/moment/moment.min.js"></script>
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/inputmask/jquery.inputmask.min.js"></script>
<!-- date-range-picker -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/daterangepicker/daterangepicker.js"></script>
<!-- bootstrap color picker -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.min.js"></script>
<!-- Tempusdominus Bootstrap 4 -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
<!-- Bootstrap Switch -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>
<!-- BS-Stepper -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/bs-stepper/js/bs-stepper.min.js"></script>
<!-- dropzonejs -->
<script src="<?php echo $GLOBALS['url_base']; ?>/plugins/dropzone/min/dropzone.min.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo $GLOBALS['url_base']; ?>/dist/js/adminlte.min.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo $GLOBALS['url_base']; ?>/dist/js/demo.js"></script>



    <!-- overlayScrollbars -->
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>

    <!-- PAGE PLUGINS -->
    <!-- jQuery Mapael -->
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/jquery-mousewheel/jquery.mousewheel.js"></script>
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/raphael/raphael.min.js"></script>
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/jquery-mapael/jquery.mapael.min.js"></script>
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/jquery-mapael/maps/usa_states.min.js"></script>
    <!-- ChartJS -->
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/chart.js/Chart.min.js"></script>

    <!-- jQuery Knob -->
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/jquery-knob/jquery.knob.min.js"></script>


    <!-- DataTables -->
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/datatables-responsive/js/dataTables.responsive.min.js"></script>
    <script src="<?php echo $GLOBALS['url_base']; ?>/plugins/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
    <script>
     function disable_reason(){
        var checkBox = document.getElementById("reason_dis");
        var element = document.getElementById("reason_dis_r");
        if(checkBox.checked == true){
            element.removeAttribute("disabled","");
            element.setAttribute("enabled","");
        } else{
            element.removeAttribute("enabled","");
            element.setAttribute("disabled","");
            element.value="";
	}
     }
     
     function parameters_form(xput){
     //https://stackoverflow.com/questions/24775725/loop-through-childnodes
     	NodeList.prototype.forEach = Array.prototype.forEach;
     	console.log(xput);
     	var clear = document.getElementById(xput+"_global").childNodes;
     	clear.forEach(function(item){
     		item.style.display = "none";
	});
 
     	var selector = document.getElementById("id_method").value;
     	if(selector != -1){
     		var id = xput+"_"+selector;
     		console.log(id);
		var div = document.getElementById(id);
		div.style.display = "block"
	} 
     }
     
    function disable_password(){
        var dropdown = document.getElementById("user_auth");
        var pass = document.getElementById("user_password");
	var pass2 = document.getElementById("user_password2");
	
        if(dropdown.value == "ldap"){
            pass.removeAttribute("enabled","");
            pass.setAttribute("disabled","");
            pass.value="";
            
            pass2.removeAttribute("enabled","");
            pass2.setAttribute("disabled","");
            pass2.value="";
            
        } else{
            pass.removeAttribute("disabled","");
            pass.setAttribute("enabled","");

            pass2.removeAttribute("disabled","");
            pass2.setAttribute("enabled","");
	}
     }
          
    </script>
    <script>
        $('.load-reyes').on('click', function(e){
            e.preventDefault();
	    $('#modal-overlay').modal('show').find('.card-body').load($(this).attr('href'));
	});
      $(function () {
        $('.tablelist').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "responsive": true,
        });

        $('.tablelist2').DataTable({
          "paging": true,
          "lengthChange": true,
          "searching": false,
          "ordering": false,
          "info": true,
          "autoWidth": false,
          "responsive": true,
        });
      
      $('#ioc_last_seen').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});
      	
      	$('#ioc_first_seen').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});   
      	   	
      	$('#ioc_reason_date').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});
      	
      	$('#edit_first_seen').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});

      	$('#edit_last_seen').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});
      	
      	$('#n_reason_date').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});

      	$('#reason_date').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});

      	$('#view_reason_date').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});
      	
      	$('#search_ioc_start_date').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});
      	
      	$('#search_ioc_end_date').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});

      	$('#whitelist_date').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});

      	$('#edit_whitelist_date').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});

  	$('#ioc_quarantine_end').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});

      	$('#ioc_monitoring_end').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});
      	  	
      	$('#edit_quarantine_end').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});

      	$('#edit_monitoring_end').datetimepicker({
      	icons:{time: 'far fa-clock'},
      	format: 'YYYY-MM-DD HH:mm'
      	});
      	
        $('.knob').knob({

          draw: function () {
          

            // "tron" case
            if (this.$.data('skin') == 'tron') {

              var a   = this.angle(this.cv)  // Angle
                ,
                  sa  = this.startAngle          // Previous start angle
                ,
                  sat = this.startAngle         // Start angle
                ,
                  ea                            // Previous end angle
                ,
                  eat = sat + a                 // End angle
                ,
                  r   = true

              this.g.lineWidth = this.lineWidth

              this.o.cursor
              && (sat = eat - 0.3)
              && (eat = eat + 0.3)

              if (this.o.displayPrevious) {
                ea = this.startAngle + this.angle(this.value)
                this.o.cursor
                && (sa = ea - 0.3)
                && (ea = ea + 0.3)
                this.g.beginPath()
                this.g.strokeStyle = this.previousColor
                this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sa, ea, false)
                this.g.stroke()
              }

              this.g.beginPath()
              this.g.strokeStyle = r ? this.o.fgColor : this.fgColor
              this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, sat, eat, false)
              this.g.stroke()

              this.g.lineWidth = 2
              this.g.beginPath()
              this.g.strokeStyle = this.o.fgColor
              this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false)
              this.g.stroke()

              return false
            }
          }
        })
        /* END JQUERY KNOB */

      });
      
    </script>
    <?php
      if(isset($GLOBALS['script_to_bottom']))
      {
        echo $GLOBALS['script_to_bottom'];
      }
    ?>

  </body>
</html>
