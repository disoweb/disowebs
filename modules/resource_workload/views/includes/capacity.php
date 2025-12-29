<h4 class="total_capacity"><?php echo _l('total_capacity').': '.(isset($data_capacity['total']['total_capacity']) ? $data_capacity['total']['total_capacity'] : 0); ?></h4>
<br>
<h5><?php echo _l('list_of_billable_projects'); ?></h5>
<div id="capacity_billable"></div>
<div class="col-md-4 col-md-offset-8">
  <table class="table text-right">
    <tbody>
      <tr id="subtotal">
        <td>
          <span class="bold"><?php echo _l('billable_expected'); ?> :</span>
        </td>
        <td class="total_billable">
          <?php echo isset($data_capacity['total']['billable']) ? html_entity_decode($data_capacity['total']['billable']) : 0; ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>
<hr/>
<h5><?php echo _l('list_of_unbillable_projects'); ?></h5>
<div id="capacity_unbillable"></div>
<div class="col-md-4 col-md-offset-8">
  <table class="table text-right">
    <tbody>
      <tr id="subtotal">
        <td>
          <span class="bold"><?php echo _l('billable_expected'); ?> :</span>
        </td>
        <td class="total_unbillable">
          <?php echo isset($data_capacity['total']['unbillable']) ? html_entity_decode($data_capacity['total']['unbillable']) : 0; ?>
        </td>
      </tr>
    </tbody>
  </table>
</div>
