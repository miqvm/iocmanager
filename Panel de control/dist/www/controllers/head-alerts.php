

<?php
/*
  $alerts = array(
    array('message' => 'Xarxa VVERD sense IPs lliures.', 'timeago'=> '3 mins', 'severity' => 'danger'),
    array('message' => 'Xarxa VROSA al 80% d\'ocupació.', 'timeago'=> '12 hores', 'severity' => 'warning'),
    array('message' => 'Missatge informatiu i llarg per veure com es comporta la part gràfica.', 'timeago'=> '3 days', 'severity' => 'info')
  );
*/
  $alerts = array();
?>

<li class="nav-item dropdown">
  <a class="nav-link" data-toggle="dropdown" href="#">
    <i class="far fa-bell"></i>
    <span class="badge <?php echo (count($alerts)>0 ? 'badge-danger':'badge-secondary'); ?> navbar-badge"><?php echo count($alerts); ?></span>
  </a>
  <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    <span class="dropdown-item dropdown-header"><?php echo count($alerts).' '._('Alerts'); ?></span>
    <div class="dropdown-divider"></div>
    <?php
      foreach ($alerts as $alert) {
        ?>
        <a href="#" class="dropdown-item">
            <!-- Message Start -->
            <div class="media">
              <div class="media-body">
                <h3 class="dropdown-item-title">
                  <span class="float-right text-sm text-<?php echo $alert['severity']; ?>"><i class="fas fa-bell"></i></span>
                </h3>
                <p class="text-sm"><?php echo $alert['message']; ?></p>
                <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> <?php echo $alert['timeago']; ?></p>
              </div>
            </div>
            <!-- Message End -->
          </a>

        <div class="dropdown-divider"></div>
        <?php
      }
    ?>
    <a href="#" class="dropdown-item dropdown-footer"><?php echo _('View all alerts'); ?></a>
  </div>
</li>
