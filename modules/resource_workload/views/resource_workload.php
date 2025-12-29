<?php defined('BASEPATH') or exit('No direct script access allowed');?>

<?php init_head();?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
                <div class="border-right">
                  <h4 class="no-margin font-bold"><?php echo _l($title); ?></h4>
                  <hr />
                </div>
                
                <!-- Mobile Filter Toggle Button -->
                <div class="mobile-filter-header visible-xs visible-sm">
                  <div class="date-quick-view">
                    <span class="date-range-display">
                      <i class="fa fa-calendar"></i>
                      <?php echo date(get_current_date_format(true), strtotime('-7 day')); ?> - <?php echo date(get_current_date_format(true)); ?>
                    </span>
                  </div>
                  <button type="button" class="btn btn-default btn-filter-toggle" data-toggle="collapse" data-target="#mobileFilters" aria-expanded="false">
                    <i class="fa fa-sliders"></i> Filters
                  </button>
                </div>
                
                <!-- Collapsible Filter Panel for Mobile -->
                <div id="mobileFilters" class="collapse mobile-filter-panel">
                  <div class="filter-section">
                    <?php if($type != 'capacity'): ?>
                    <div class="filter-item">
                      <label><?php echo _l('department'); ?></label>
                      <?php echo render_select('department_mobile', $departments, array('departmentid', 'name'), '', '', array('multiple' => true, 'data-actions-box' => true, 'class' => 'mobile-select'), array(), '', '', false); ?>
                    </div>
                    <div class="filter-item">
                      <label><?php echo _l('role'); ?></label>
                      <?php echo render_select('role_mobile', $roles, array('roleid', 'name'), '', '', array('multiple' => true, 'data-actions-box' => true, 'class' => 'mobile-select'), array(), '', '', false); ?>
                    </div>
                    <?php endif; ?>
                    <div class="filter-item">
                      <label><?php echo _l('project'); ?></label>
                      <?php echo render_select('project_mobile', $projects, array('id', 'name'), '', '', array('multiple' => true, 'data-actions-box' => true, 'class' => 'mobile-select'), array(), '', '', false); ?>
                    </div>
                    <?php if($type != 'capacity'): ?>
                    <div class="filter-item">
                      <label><?php echo _l('staff'); ?></label>
                      <?php echo render_select('staff_mobile', $staffs, array('staffid', 'firstname', 'lastname'), '', '', array('multiple' => true, 'data-actions-box' => true, 'class' => 'mobile-select'), array(), '', '', false); ?>
                    </div>
                    <?php endif; ?>
                    <div class="filter-item">
                      <label><?php echo _l('from_date'); ?></label>
                      <?php echo render_date_input('from_date_mobile', '', date(get_current_date_format(true), strtotime('-7 day', strtotime(date('Y-m-d'))))); ?>
                    </div>
                    <div class="filter-item">
                      <label><?php echo _l('to_date'); ?></label>
                      <?php echo render_date_input('to_date_mobile', '', date(get_current_date_format(true))); ?>
                    </div>
                  </div>
                  <div class="filter-actions">
                    <button type="button" class="btn btn-default btn-block" onclick="clearMobileFilters()">
                      <i class="fa fa-times"></i> Clear
                    </button>
                    <button type="button" class="btn btn-info btn-block" onclick="applyMobileFilters()">
                      <i class="fa fa-search"></i> Apply Filters
                    </button>
                  </div>
                </div>
                
                <!-- Desktop Filters (hidden on mobile) -->
                <div class="row hidden-xs hidden-sm">
                  <div id="kanban-params">
                    <div class="col-md-2 <?php if($type == 'capacity'){echo 'hide';} ?>">
                      <?php echo render_select('department', $departments, array('departmentid', 'name'), 'department', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
                    </div>
                    <div class="col-md-2 <?php if($type == 'capacity'){echo 'hide';} ?>">
                      <?php echo render_select('role', $roles, array('roleid', 'name'), 'role', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
                    </div>
                    <div class="col-md-2">
                      <?php echo render_select('project', $projects, array('id', 'name'), 'project', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
                    </div>
                    <div class="col-md-2 <?php if($type == 'capacity'){echo 'hide';} ?>">
                      <?php echo render_select('staff', $staffs, array('staffid', 'firstname', 'lastname'), 'staff', '', array('multiple' => true, 'data-actions-box' => true), array(), '', '', false); ?>
                    </div>
                    <div class="col-md-4">
                      <div class="col-md-5">
                        <?php echo render_date_input('from_date', 'from_date', date(get_current_date_format(true), strtotime('-7 day', strtotime(date('Y-m-d'))))); ?>
                      </div>
                      <div class="col-md-5">
                        <?php echo render_date_input('to_date', 'to_date', date(get_current_date_format(true))); ?>
                      </div>
                      <div class="col-md-2">
                        <a href="#" onclick="get_data_workload(); return false;" class="px-0 btn btn-info display-block mtop25"><i class="fa fa-search"></i></a>
                      </div>
                    </div>
                  </div>
                </div>
                
            <div class="horizontal-tabs mb-5">
              <ul class="nav nav-tabs nav-tabs-horizontal mb-10">
              <?php
              foreach($tab as $gr){ ?> 
                <li<?php if($type == $gr){echo " class='active'"; } ?>>
                <a href="<?php echo admin_url('resource_workload?type='.$gr); ?>" data-group="<?php echo html_entity_decode($gr); ?>">
                  <?php echo _l($gr); ?>
                  </a>
                </li>
                <?php 
              } ?>
              </ul>
              </div>
              <?php $this->load->view($tabs['view']); ?>
              <br>
              </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail();?>
<script>
// Mobile filter functions
function applyMobileFilters() {
    // Sync mobile filters to desktop hidden ones
    $('#department').val($('#department_mobile').val()).trigger('change');
    $('#role').val($('#role_mobile').val()).trigger('change');
    $('#project').val($('#project_mobile').val()).trigger('change');
    $('#staff').val($('#staff_mobile').val()).trigger('change');
    $('#from_date').val($('#from_date_mobile').val());
    $('#to_date').val($('#to_date_mobile').val());
    
    // Update date display
    $('.date-range-display').html('<i class="fa fa-calendar"></i> ' + $('#from_date_mobile').val() + ' - ' + $('#to_date_mobile').val());
    
    // Collapse filter panel
    $('#mobileFilters').collapse('hide');
    
    // Trigger search
    get_data_workload();
}

function clearMobileFilters() {
    $('#department_mobile').val([]).trigger('change');
    $('#role_mobile').val([]).trigger('change');
    $('#project_mobile').val([]).trigger('change');
    $('#staff_mobile').val([]).trigger('change');
    $('#from_date_mobile').val('<?php echo date(get_current_date_format(true), strtotime('-7 day')); ?>');
    $('#to_date_mobile').val('<?php echo date(get_current_date_format(true)); ?>');
}
</script>
<?php require 'modules/resource_workload/assets/js/resource_workload_js.php';?>
</body>
</html>
