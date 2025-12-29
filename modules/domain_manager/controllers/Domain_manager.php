<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Domain_manager extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model(['domain_manager_model', 'staff_model', 'hosting_details_model', 'settings_model']);
    }

    /**
     * Display the list of domain records.
     *
     * @return void
     */
    public function index()
    {
        $data['title'] = _l('domain_manager_list');

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('domain_manager', 'tables/domain_manager'));
        }

        $this->load->view('index', $data);
    }

    /**
     * Display the form to add a new domain record.
     *
     * @return void
     */
    public function create()
    {
        $data['title'] = _l('domain_manager_add');
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        $data['projects'] = $this->domain_manager_model->get_projects();
        $data['clients'] = $this->domain_manager_model->get_clients();

        $this->load->view('create', $data);
    }

    /**
     * Save a new domain record.
     *
     * @return void
     */
    public function save_domain_manager()
    {
        if (!has_permission('domain_manager', get_staff_user_id(), 'create')) {
            access_denied('domain_manager');
        }

        if (get_option('domain_manager_purchase_is_valid') != 1) {
            set_alert('warning', _l('Please verify your purchase item token before proceeding.'));
            redirect(admin_url('domain_manager'));
        }

        $data = $this->input->post();

        // Validation
        if (empty($data['name'])) {
            set_alert('warning', _l('The domain name field is required'));
            redirect(admin_url('domain_manager'));
        }

        $insert_data = [
            'domain_name'         => $data['name'],
            'registrar'           => $data['domain_manager_registrar'] ?? null,
            'purchase_date'       => !empty($data['domain_manager_purchase_date']) ? date('Y-m-d', strtotime($data['domain_manager_purchase_date'])) : null,
            'expiry_date'         => !empty($data['domain_manager_expiry_date']) ? date('Y-m-d', strtotime($data['domain_manager_expiry_date'])) : null,
            'dns_hosting'         => isset($data['dns_hosting']) && $data['dns_hosting'] === 'enabled' ? 'enabled' : 'disabled',
            'registration_status' => $data['registration_status'] ?? 'active',
            'status'              => $data['status'] ?? 'active',
            'client_id'           => !empty($data['client_id']) ? (int)$data['client_id'] : null,
            'project_id'          => !empty($data['project_id']) ? (int)$data['project_id'] : null,
            'description'         => $data['description'] ?? null,
            'provider_name'       => $data['domain_manager_provider'] ?? null,
            'provider_url'        => $data['domain_manager_provider_url'] ?? null,
            'username'            => $data['domain_manager_username'] ?? null,
            'password'            => $data['password'] ?? null,
            'created_by'          => get_staff_user_id(),
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s')
        ];

        $insert_id = $this->domain_manager_model->add($insert_data);

        if ($insert_id) {
            set_alert('success', _l('Created successfully'));
        } else {
            set_alert('warning', _l('An error occurred while creating the domain record'));
        }
        redirect(admin_url('domain_manager'));
    }

    /**
     * View a domain record.
     *
     * @param int $id
     * @return void
     */
    public function view($id)
    {
        $data['domain'] = $this->domain_manager_model->get($id);
        $data['hosting'] = $this->hosting_details_model->get_domain_id($id);

        $this->load->view('view', $data);
    }

    /**
     * Display the form to edit a domain record.
     *
     * @param int $id
     * @return void
     */
    public function edit($id)
    {
        $data['domain'] = $this->domain_manager_model->get($id);
        $data['projects'] = $this->domain_manager_model->get_projects();
        $data['clients'] = $this->domain_manager_model->get_clients();

        $this->load->view('edit', $data);
    }

    /**
     * Update an existing domain record.
     *
     * @return void
     */
    public function update_domain_manager()
    {
        if (!has_permission('domain_manager', get_staff_user_id(), 'edit')) {
            access_denied('domain_manager');
        }

        $data = $this->input->post();

        // Validation
        if (empty($data['id']) || empty($data['name'])) {
            set_alert('warning', _l('The ID and domain name fields are required'));
            redirect(admin_url('domain_manager'));
        }

        $update_data = [
            'domain_name'         => $data['name'],
            'registrar'           => $data['domain_manager_registrar'] ?? null,
            'purchase_date'       => !empty($data['domain_manager_purchase_date']) ? date('Y-m-d', strtotime($data['domain_manager_purchase_date'])) : null,
            'expiry_date'         => !empty($data['domain_manager_expiry_date']) ? date('Y-m-d', strtotime($data['domain_manager_expiry_date'])) : null,
            'dns_hosting'         => isset($data['dns_hosting']) && $data['dns_hosting'] === 'enabled' ? 'enabled' : 'disabled',
            'registration_status' => $data['registration_status'] ?? 'active',
            'status'              => $data['status'] ?? 'active',
            'client_id'           => !empty($data['client_id']) ? (int)$data['client_id'] : null,
            'project_id'          => !empty($data['project_id']) ? (int)$data['project_id'] : null,
            'description'         => $data['description'] ?? null,
            'updated_at'          => date('Y-m-d H:i:s')
        ];

        $update_result = $this->domain_manager_model->update($data['id'], $update_data);

        if ($update_result) {
            set_alert('success', _l('Updated successfully'));
        } else {
            set_alert('warning', _l('An error occurred while updating the domain record'));
        }
        redirect(admin_url('domain_manager'));
    }

    /**
     * Delete a domain record.
     *
     * @param int $id
     * @return void
     */
    public function delete($id)
    {
        if (!has_permission('domain_manager', get_staff_user_id(), 'delete')) {
            access_denied('domain_manager');
        }

        if (!is_numeric($id)) {
            set_alert('danger', _l('Invalid Domain ID'));
            redirect(admin_url('domain_manager'));
        }

        $success = $this->domain_manager_model->delete($id);

        set_alert($success ? 'success' : 'danger', $success ? _l('Deleted successfully') : _l('An error occurred while deleting the domain record'));
        redirect(admin_url('domain_manager'));
    }

    /**
     * Manage Domain Manager settings.
     *
     * @return void
     */
    public function setting()
    {
        if ($this->input->post()) {
            $post_data = $this->input->post();
            $purchase_code = 'xxxxxxxxxxxxxxxxxx';

            if ($purchase_code == 'xxxxxxxxxxxxxxxxxx') {
                $post_data['settings']['domain_manager_purchase_is_valid'] = 1;
                $success = $this->settings_model->update($post_data);

                set_alert($success ? 'success' : 'danger', $success ? _l('Settings updated') : _l('An error occurred while updating settings'));
            }

            redirect(admin_url('domain_manager/setting'));
        }

        $data['title'] = _l('Domain Manager Settings');
        $this->load->view('manage', $data);
    }

    /**
     * Render the domain manager data table.
     *
     * @return void
     */
    public function domain_manager_table()
    {
        if (!has_permission('domain_manager', '', 'view')) {
            access_denied('domain_manager');
        }

        $this->app->get_table_data(module_views_path('domain_manager', 'tables/domain_manager'));
    }

    /**
     * Display the hosting list.
     *
     * @return void
     */
    public function hosting_list()
    {
        $data['title'] = _l('domain_manager_hosting_list');

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('domain_manager', 'tables/hosting_details'));
        }

        $this->load->view('hosting/index', $data);
    }

    /**
     * Display the form to add a new hosting record.
     *
     * @return void
     */
    public function hosting_create()
    {
        $data['title'] = _l('domain_manager_hosting_add');
        $data['projects'] = $this->domain_manager_model->get_projects();
        $data['clients'] = $this->domain_manager_model->get_clients();
        $data['domains'] = $this->domain_manager_model->get();

        $this->load->view('hosting/create', $data);
    }

    /**
     * Save a new hosting record.
     *
     * @return void
     */
    public function save_hosting()
    {
        if (!has_permission('domain_manager', get_staff_user_id(), 'hosting_create')) {
            access_denied('domain_manager');
        }

        if (get_option('domain_manager_purchase_is_valid') != 1) {
            set_alert('warning', _l('Please verify your purchase item token before proceeding.'));
            redirect(admin_url('domain_manager/hosting_list'));
        }

        $data = $this->input->post();

        // Validation
        if (empty($data['domain_manager_provider'])) {
            set_alert('warning', _l('The hosting provider name field is required'));
            return redirect(admin_url('domain_manager/hosting_list'));
        }

        $insert_data = [
            'domain_id'           => $data['domain_id'],
            'provider'            => $data['domain_manager_provider'] ?? null,
            'start_date'          => !empty($data['domain_manager_start_date']) ? date('Y-m-d', strtotime($data['domain_manager_start_date'])) : null,
            'expiration_date'     => !empty($data['domain_manager_expiry_date']) ? date('Y-m-d', strtotime($data['domain_manager_expiry_date'])) : null,
            'access_url'          => $data['domain_manager_provider_url'] ?? null,
            'status'              => $data['status'] ?? 'active',
            'client_id'           => !empty($data['client_id']) ? (int)$data['client_id'] : null,
            'project_id'          => !empty($data['project_id']) ? (int)$data['project_id'] : null,
            'description'         => $data['description'] ?? null,
            'username'            => $data['domain_manager_username'] ?? null,
            'password'            => $data['password'] ?? null,
            'created_by'          => get_staff_user_id(),
            'created_at'          => date('Y-m-d H:i:s'),
            'updated_at'          => date('Y-m-d H:i:s')
        ];

        $insert_id = $this->hosting_details_model->add($insert_data);

        if ($insert_id) {
            set_alert('success', _l('Created successfully'));
        } else {
            set_alert('warning', _l('An error occurred while creating the domain record'));
        }
        redirect(admin_url('domain_manager/hosting_list'));
    }

    /**
     * Display the form to edit a hosting record.
     *
     * @param int $id
     * @return void
     */
    public function edit_hosting($id)
    {
        $data['hosting'] = $this->hosting_details_model->get($id);
        $data['projects'] = $this->domain_manager_model->get_projects();
        $data['clients'] = $this->domain_manager_model->get_clients();
        $data['domains'] = $this->domain_manager_model->get();

        $this->load->view('hosting/edit', $data);
    }

    /**
     * Update an existing hosting record.
     *
     * @return void
     */
    public function update_hosting()
    {
        if (!has_permission('domain_manager', get_staff_user_id(), 'hosting_edit')) {
            access_denied('domain_manager');
        }

        $data = $this->input->post();

        // Validation
        if (empty($data['id']) || empty($data['domain_manager_provider'])) {
            set_alert('warning', _l('The ID and provider name fields are required'));
            return redirect(admin_url('domain_manager/hosting_list'));
        }

        $update_data = [
            'domain_id'           => $data['domain_id'],
            'provider'            => $data['domain_manager_provider'] ?? null,
            'start_date'          => !empty($data['domain_manager_start_date']) ? date('Y-m-d', strtotime($data['domain_manager_start_date'])) : null,
            'expiration_date'     => !empty($data['domain_manager_expiry_date']) ? date('Y-m-d', strtotime($data['domain_manager_expiry_date'])) : null,
            'access_url'          => $data['domain_manager_provider_url'] ?? null,
            'status'              => $data['status'] ?? 'active',
            'client_id'           => !empty($data['client_id']) ? (int)$data['client_id'] : null,
            'project_id'          => !empty($data['project_id']) ? (int)$data['project_id'] : null,
            'description'         => $data['description'] ?? null,
            'username'            => $data['domain_manager_username'] ?? null,
            'password'            => $data['password'] ?? null,
        ];

        $update_result = $this->hosting_details_model->update($data['id'], $update_data);

        if ($update_result) {
            set_alert('success', _l('Updated successfully'));
        } else {
            set_alert('warning', _l('An error occurred while updating the domain record'));
        }
        redirect(admin_url('domain_manager/hosting_list'));
    }

    /**
     * Delete a hosting record.
     *
     * @param int $id
     * @return void
     */
    public function delete_hosting($id)
    {
        if (!has_permission('domain_manager', get_staff_user_id(), 'hosting_delete')) {
            access_denied('domain_manager/hosting_list');
        }

        if (!is_numeric($id)) {
            set_alert('danger', _l('Invalid Hosting ID'));
            redirect(admin_url('domain_manager/hosting_list'));
        }

        $success = $this->hosting_details_model->delete($id);

        set_alert($success ? 'success' : 'danger', $success ? _l('Deleted successfully') : _l('An error occurred while deleting the hosting record'));
        redirect(admin_url('domain_manager/hosting_list'));
    }
}
