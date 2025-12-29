<?php

defined('BASEPATH') or exit('No direct script access allowed');

$where = [];

if(isset($_GET['client']) && !empty($_GET['client'])){
    $where = [
        'AND client_id=' . $_GET['client'],
    ];
}
if(isset($_GET['project']) && !empty($_GET['project'])){
    $where = [
        'AND project_id=' . $_GET['project'],
    ];
}
$join = [
    'LEFT JOIN ' . db_prefix() . 'domain_manager ON ' . db_prefix() . 'domain_manager.id = ' . db_prefix() . 'hosting_details.domain_id',
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'hosting_details.project_id',
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'hosting_details.client_id',
];
$aColumns = [
    db_prefix() . 'hosting_details.id',
    db_prefix() . 'hosting_details.provider',
    db_prefix() . 'domain_manager.domain_name',
    db_prefix() . 'clients.company',
    db_prefix() . 'projects.name',
    db_prefix() . 'hosting_details.start_date',
    db_prefix() . 'hosting_details.expiration_date',
    db_prefix() . 'hosting_details.status',
];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'hosting_details';
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'hosting_details.client_id',
    db_prefix() . 'hosting_details.project_id',
    db_prefix() . 'hosting_details.access_url',
    db_prefix() . 'hosting_details.domain_id',
]);
$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        
        if ($aColumns[$i] == db_prefix() . 'hosting_details.provider') {
            $_data =   e($_data) ;

            $_data .= '<div class="row-options">';
            
           
            if (staff_can('hosting_edit',  'domain_manager')) {
                 $_data .= ' <a href="' . admin_url('domain_manager/edit_hosting/' . $aRow[db_prefix() . 'hosting_details.id']) . '">' . _l('edit') . '</a>';
            }

            if (staff_can('hosting_delete',  'domain_manager')) {
                $_data .= ' | <a href="' . admin_url('domain_manager/delete_hosting/' . $aRow[db_prefix() . 'hosting_details.id']) . '" class="_delete">' . _l('delete') . '</a>';
            }

            $_data .= '</div>';
        }elseif($aColumns[$i] ==  db_prefix() . 'domain_manager.domain_name'){
            $_data = '<a href="' . admin_url('domain_manager/view/' . $aRow['domain_id']) . '">' . e($_data) . '</a>';

        } elseif ($aColumns[$i] == db_prefix() . 'hosting_details.start_date' ) {

            if ($_data == '0000-00-00' || $_data == null) {
                $_data = " - ";
            }else{
                $_data = "<span>".e(_d($_data))."</span>";
            }
         
        }  elseif ($aColumns[$i] == db_prefix() . 'hosting_details.expiration_date') {
            if ($_data == '0000-00-00' || $_data == null) {
                $_data = " - ";
            }else{
                // Convert the date string to a timestamp
                $expiryTime = strtotime($_data);
                $currentTime = time();
                // Calculate timestamp for one month from now
                $oneMonthAhead = strtotime("+1 month", $currentTime);

                // If the expiry date is in the past, it's expired
                if ($expiryTime < $currentTime) {
                    $_data = "<span class='text-danger'>".e(_d($_data))."</span>";
                }
                // If the expiry date is within the next month, it's expiring soon
                elseif ($expiryTime <= $oneMonthAhead) {
                    $_data = "<span class='text-warning'>".e(_d($_data))."</span>";
                }
                // Otherwise, it's still active
                else {
                    $_data = "<span class='text-success'>".e(_d($_data))."</span>";
                }
            }
        
          
        } elseif ($aColumns[$i] == db_prefix() . 'hosting_details.status') {
            if($_data == 'active'){
                $_data = '<span class="label text-success" style="border: 1px solid rgb(0, 175, 53);">'.strtoupper(_l($_data)).'</span>';
            }else{
                $_data = '<span class="label text-danger " style="border: 1px solid #ff1100;">'._l($_data).'</span>';
            }
        }elseif($aColumns[$i] == db_prefix() .'clients.company'){
            $_data = '<a href="' . admin_url('clients/client/' . $aRow['client_id']) . '">' . e($_data) . '</a>';
            
        }elseif($aColumns[$i] == db_prefix() .'projects.name'){
            $_data = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . e($_data) . '</a>';
        }elseif($aColumns[$i] == db_prefix() . 'hosting_details.id'){
            $_data = '<a href="' . admin_url('hosting_details/view/' . $_data) . '">' . e($_data) . '</a>';
        }else{
            $_data = $_data;
        }
        
        
        $row[] = $_data;
    }
    $row[] = '<a  data-toggle="tooltip" data-title="'._l('domain_access_url').'" class="btn btn-sm btn-default text-white" target="_blank" href="' . (strpos($aRow['access_url'], 'https://') === 0 ? $aRow['access_url'] : 'https://' . $aRow['access_url']) . '"><i class="fa fa-link"></i></a>';
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
