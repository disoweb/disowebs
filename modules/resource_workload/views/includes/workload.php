<label class="hidden-xs hidden-sm"><?php echo _l('note_workload'); ?></label>

<!-- Mobile Card View -->
<div class="workload-mobile-view visible-xs visible-sm">
  <div class="mobile-summary-cards">
    <div class="row">
      <div class="col-xs-6">
        <div class="summary-card summary-capacity">
          <div class="summary-value"><?php echo isset($data_workload['data_total']['capacity']) ? round($data_workload['data_total']['capacity'], 2) : 0; ?></div>
          <div class="summary-label"><?php echo _l('total_capacity'); ?></div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="summary-card summary-estimate">
          <div class="summary-value"><?php echo isset($data_workload['data_total']['estimate']) ? round($data_workload['data_total']['estimate'], 2) : 0; ?></div>
          <div class="summary-label"><?php echo _l('total_estimated_time'); ?></div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="summary-card summary-spent">
          <div class="summary-value"><?php echo isset($data_workload['data_total']['spent_time']) ? round($data_workload['data_total']['spent_time'], 2) : 0; ?></div>
          <div class="summary-label"><?php echo _l('total_spent_time'); ?></div>
        </div>
      </div>
      <div class="col-xs-6">
        <div class="summary-card summary-available">
          <?php 
            $capacity = isset($data_workload['data_total']['capacity']) ? $data_workload['data_total']['capacity'] : 0;
            $estimate = isset($data_workload['data_total']['estimate']) ? $data_workload['data_total']['estimate'] : 0;
          ?>
          <div class="summary-value"><?php echo round(($capacity - $estimate), 2); ?></div>
          <div class="summary-label"><?php echo _l('total_available_cap'); ?></div>
        </div>
      </div>
    </div>
  </div>
  
  <h5 class="mtop15 mbot10"><strong><?php echo _l('staff'); ?></strong></h5>
  
  <?php if(isset($data_workload['data']) && !empty($data_workload['data'])): ?>
    <?php foreach($data_workload['data'] as $staff): ?>
      <div class="staff-workload-card">
        <div class="staff-card-header">
          <div class="staff-name"><?php echo isset($staff['staff_name']) ? $staff['staff_name'] : 'N/A'; ?></div>
          <div class="staff-role"><?php echo isset($staff['staff_role']) ? $staff['staff_role'] : ''; ?></div>
        </div>
        <div class="staff-card-body">
          <div class="staff-stat">
            <span class="stat-label"><?php echo _l('department'); ?>:</span>
            <span class="stat-value"><?php echo isset($staff['staff_department']) ? $staff['staff_department'] : '-'; ?></span>
          </div>
          <div class="staff-stat">
            <span class="stat-label"><?php echo _l('capacity'); ?>:</span>
            <span class="stat-value"><?php echo isset($staff['capacity']) ? $staff['capacity'] : 0; ?> hrs</span>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <div class="alert alert-info"><?php echo _l('no_data_found'); ?></div>
  <?php endif; ?>
  
  <p class="text-muted mtop15 text-center">
    <small><i class="fa fa-info-circle"></i> <?php echo _l('swipe_to_see_full_table'); ?></small>
  </p>
</div>

<!-- Desktop Table View -->
<div class="workload-desktop-view hidden-xs hidden-sm">
  <div id="workload"></div>
</div>

<!-- Responsive Table View (tablet/mobile landscape) -->
<div class="workload-table-responsive visible-xs visible-sm">
  <p class="text-muted mbot10"><small><i class="fa fa-hand-o-right"></i> Swipe left/right to see more</small></p>
  <div class="table-scroll-wrapper">
    <div id="workload-mobile"></div>
  </div>
</div>

<div class="col-md-4 col-md-offset-8 hidden-xs hidden-sm">
  <table class="table text-right">
    <tbody>
      <tr id="subtotal">
        <td><span class="bold"><?php echo _l('total_capacity'); ?> :</span>
        </td>
        <td class="total_capacity">
          <?php echo isset($data_workload['data_total']['capacity']) ? round($data_workload['data_total']['capacity'], 2) : 0; ?>
        </td>
      </tr>
      <tr>
        <td><span class="bold"><?php echo _l('total_estimated_time'); ?> :</span>
        </td>
        <td class="total_estimated_time">
          <?php echo isset($data_workload['data_total']['estimate']) ? round($data_workload['data_total']['estimate'], 2) : 0; ?>
        </td>
      </tr>
      <tr>
        <td><span class="bold"><?php echo _l('total_spent_time'); ?> :</span>
        </td>
        <td class="total_spent_time">                                      
          <?php echo isset($data_workload['data_total']['spent_time']) ? round($data_workload['data_total']['spent_time'], 2) : 0; ?>
        </td>
      </tr>
      <tr>
        <td><span class="bold"><?php echo _l('total_available_cap'); ?> :</span>
        </td>
        <td class="total_available_cap">                                      
          <?php 
            $capacity = isset($data_workload['data_total']['capacity']) ? $data_workload['data_total']['capacity'] : 0;
            $estimate = isset($data_workload['data_total']['estimate']) ? $data_workload['data_total']['estimate'] : 0;
            echo round(($capacity - $estimate), 2); 
          ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>
