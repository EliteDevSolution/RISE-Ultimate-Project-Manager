<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Leads extends MY_Controller {

    function __construct() {
        parent::__construct();

        //check permission to access this module
        $this->init_permission_checker("lead");
    }

    /* load leads list view */

    function index() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_lead");

        $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);

        $view_data['lead_statuses'] = $this->Lead_status_model->get_details()->result();
        $view_data['lead_sources'] = $this->Lead_source_model->get_details()->result();
        $view_data['owners_dropdown'] = $this->_get_owners_dropdown("filter");

        $this->template->rander("leads/index", $view_data);
    }

    /* load lead add/edit modal */

    function modal_form() {
        $lead_id = $this->input->post('id');
        $this->can_access_this_lead($lead_id);
        $view_data = $this->make_lead_modal_form_data($lead_id);
        $this->load->view('leads/modal_form', $view_data);
    }

    private function make_lead_modal_form_data($lead_id = 0) {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "numeric"
        ));

        $view_data['label_column'] = "col-md-3";
        $view_data['field_column'] = "col-md-9";

        $view_data["view"] = $this->input->post('view'); //view='details' needed only when loding from the lead's details view
        $view_data['model_info'] = $this->Clients_model->get_one($lead_id);
        $view_data["currency_dropdown"] = $this->_get_currency_dropdown_select2_data();
        $view_data["owners_dropdown"] = $this->_get_owners_dropdown();

        $view_data['statuses'] = $this->Lead_status_model->get_details()->result();
        $view_data['sources'] = $this->Lead_source_model->get_details()->result();

        //prepare groups dropdown list
        $view_data['groups_dropdown'] = $this->_get_groups_dropdown_select2_data();

        //get custom fields
        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("leads", $lead_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

        return $view_data;
    }

    //get owners dropdown
    //owner will be team member
    private function _get_owners_dropdown($view_type = "") {
        $team_members = $this->Users_model->get_all_where(array("user_type" => "staff", "deleted" => 0, "status" => "active"))->result();
        $team_members_dropdown = array();

        if ($view_type == "filter") {
            $team_members_dropdown = array(array("id" => "", "text" => "- " . lang("owner") . " -"));
        }

        foreach ($team_members as $member) {
            $team_members_dropdown[] = array("id" => $member->id, "text" => $member->first_name . " " . $member->last_name);
        }

        return $team_members_dropdown;
    }

    /* insert or update a lead */

    function save() {
        $client_id = $this->input->post('id');
        $this->can_access_this_lead($client_id);

        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "numeric",
            "company_name" => "required"
        ));

        $data = array(
            "company_name" => $this->input->post('company_name'),
            "address" => $this->input->post('address'),
            "city" => $this->input->post('city'),
            "state" => $this->input->post('state'),
            "zip" => $this->input->post('zip'),
            "country" => $this->input->post('country'),
            "phone" => $this->input->post('phone'),
            "website" => $this->input->post('website'),
            "vat_number" => $this->input->post('vat_number'),
            "currency_symbol" => $this->input->post('currency_symbol') ? $this->input->post('currency_symbol') : "",
            "currency" => $this->input->post('currency') ? $this->input->post('currency') : "",
            "is_lead" => 1,
            "lead_status_id" => $this->input->post('lead_status_id'),
            "lead_source_id" => $this->input->post('lead_source_id'),
            "owner_id" => $this->input->post('owner_id') ? $this->input->post('owner_id') : $this->login_user->id
        );


        if (!$client_id) {
            $data["created_date"] = get_current_utc_time();
        }


        $data = clean_data($data);

        $save_id = $this->Clients_model->save($data, $client_id);
        if ($save_id) {
            save_custom_fields("leads", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            if (!$client_id) {
                log_notification("lead_created", array("lead_id" => $save_id), $this->login_user->id);
            }

            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, 'view' => $this->input->post('view'), 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* delete or undo a lead */

    function delete() {
        $this->access_only_allowed_members();

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        $this->can_access_this_lead($id);

        if ($this->Clients_model->delete_client_and_sub_items($id)) {
            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    private function show_own_leads_only_user_id() {
        if ($this->login_user->user_type === "staff") {
            return get_array_value($this->login_user->permissions, "lead") == "own" ? $this->login_user->id : false;
        }
    }

    private function can_access_this_lead($lead_id = 0) {
        $lead_info = $this->Clients_model->get_one($lead_id);

        if ($lead_info->id && get_array_value($this->login_user->permissions, "lead") == "own" && $lead_info->owner_id !== $this->login_user->id) {
            redirect("forbidden");
        }
    }

    /* list of leads, prepared for datatable  */

    function list_data() {
        $this->access_only_allowed_members();
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);

        $show_own_leads_only_user_id = $this->show_own_leads_only_user_id();

        $options = array(
            "custom_fields" => $custom_fields,
            "leads_only" => true,
            "status" => $this->input->post('status'),
            "source" => $this->input->post('source'),
            "owner_id" => $show_own_leads_only_user_id ? $show_own_leads_only_user_id : $this->input->post('owner_id')
        );

        $list_data = $this->Clients_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of lead list table */

    private function _row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("leads", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "custom_fields" => $custom_fields,
            "leads_only" => true
        );
        $data = $this->Clients_model->get_details($options)->row();
        return $this->_make_row($data, $custom_fields);
    }

    /* prepare a row of lead list table */

    private function _make_row($data, $custom_fields) {
        //primary contact 
        $image_url = get_avatar($data->contact_avatar);
        $contact = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->primary_contact";
        $primary_contact = get_lead_contact_profile_link($data->primary_contact_id, $contact);

        //lead owner
        $owner = "-";
        if ($data->owner_id) {
            $owner_image_url = get_avatar($data->owner_avatar);
            $owner_user = "<span class='avatar avatar-xs mr10'><img src='$owner_image_url' alt='...'></span> $data->owner_name";
            $owner = get_team_member_profile_link($data->owner_id, $owner_user);
        }

        $row_data = array(
            anchor(get_uri("leads/view/" . $data->id), $data->company_name),
            $data->primary_contact ? $primary_contact : "",
            $owner
        );

        $row_data[] = js_anchor($data->lead_status_title, array("style" => "background-color: $data->lead_status_color", "class" => "label", "data-id" => $data->id, "data-value" => $data->lead_status_id, "data-act" => "update-lead-status"));

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id), true);
        }

        $row_data[] = modal_anchor(get_uri("leads/modal_form"), "<i class='fa fa-pencil'></i>", array("class" => "edit", "title" => lang('edit_lead'), "data-post-id" => $data->id))
                . js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_lead'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("leads/delete"), "data-action" => "delete-confirmation"));

        return $row_data;
    }

    /* load lead details view */

    function view($client_id = 0, $tab = "") {
        $this->check_module_availability("module_lead");
        $this->access_only_allowed_members();

        if ($client_id) {
            $options = array("id" => $client_id);
            $lead_info = $this->Clients_model->get_details($options)->row();
            $this->can_access_this_lead($client_id);

            if ($lead_info && $lead_info->is_lead) {

                $access_info = $this->get_access_info("estimate");
                $view_data["show_estimate_info"] = (get_setting("module_estimate") && $access_info->access_type == "all") ? true : false;

                $access_info = $this->get_access_info("estimate_request");
                $view_data["show_estimate_request_info"] = (get_setting("module_estimate_request") && $access_info->access_type == "all") ? true : false;

                /*
                  $access_info = $this->get_access_info("ticket");
                  $view_data["show_ticket_info"] = (get_setting("module_ticket") && $access_info->access_type == "all") ? true : false;
                 */

                $view_data["show_ticket_info"] = false; //don't show tickets for now.

                $view_data["show_note_info"] = (get_setting("module_note")) ? true : false;
                $view_data["show_event_info"] = (get_setting("module_event")) ? true : false;

                $view_data['lead_info'] = $lead_info;

                $view_data["tab"] = $tab;

                $this->template->rander("leads/view", $view_data);
            } else {
                show_404();
            }
        } else {
            show_404();
        }
    }

    /* load estimates tab  */

    function estimates($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $this->can_access_this_lead($client_id);
            $view_data["lead_info"] = $this->Clients_model->get_one($client_id);
            $view_data['client_id'] = $client_id;

            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("estimates", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("leads/estimates/estimates", $view_data);
        }
    }

    /* load estimate requests tab  */

    function estimate_requests($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $this->can_access_this_lead($client_id);
            $view_data['client_id'] = $client_id;
            $this->load->view("leads/estimates/estimate_requests", $view_data);
        }
    }

    /* load notes tab  */

    function notes($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $this->can_access_this_lead($client_id);
            $view_data['client_id'] = $client_id;
            $this->load->view("leads/notes/index", $view_data);
        }
    }

    /* load events tab  */

    function events($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $this->can_access_this_lead($client_id);
            $view_data['client_id'] = $client_id;
            $view_data['calendar_filter_dropdown'] = $this->get_calendar_filter_dropdown("lead");
            $view_data['event_labels_dropdown'] = json_encode($this->make_labels_dropdown("event", "", true, lang("event") . " " . strtolower(lang("label"))));
            $this->load->view("events/index", $view_data);
        }
    }

    /* load files tab */

    function files($client_id) {

        $this->access_only_allowed_members();
        $this->can_access_this_lead($client_id);

        $options = array("client_id" => $client_id);
        $view_data['files'] = $this->General_files_model->get_details($options)->result();
        $view_data['client_id'] = $client_id;
        $this->load->view("leads/files/index", $view_data);
    }

    /* file upload modal */

    function file_modal_form() {
        $view_data['model_info'] = $this->General_files_model->get_one($this->input->post('id'));
        $client_id = $this->input->post('client_id') ? $this->input->post('client_id') : $view_data['model_info']->client_id;

        $this->access_only_allowed_members();
        $this->can_access_this_lead($client_id);

        $view_data['client_id'] = $client_id;
        $this->load->view('leads/files/modal_form', $view_data);
    }

    /* save file data and move temp file to parmanent file directory */

    function save_file() {


        validate_submitted_data(array(
            "id" => "numeric",
            "client_id" => "required|numeric"
        ));

        $client_id = $this->input->post('client_id');
        $this->access_only_allowed_members();
        $this->can_access_this_lead($client_id);


        $files = $this->input->post("files");
        $success = false;
        $now = get_current_utc_time();

        $target_path = getcwd() . "/" . get_general_file_path("client", $client_id);

        //process the fiiles which has been uploaded by dropzone
        if ($files && get_array_value($files, 0)) {
            foreach ($files as $file) {
                $file_name = $this->input->post('file_name_' . $file);
                $file_info = move_temp_file($file_name, $target_path);
                if ($file_info) {
                    $data = array(
                        "client_id" => $client_id,
                        "file_name" => get_array_value($file_info, 'file_name'),
                        "file_id" => get_array_value($file_info, 'file_id'),
                        "service_type" => get_array_value($file_info, 'service_type'),
                        "description" => $this->input->post('description_' . $file),
                        "file_size" => $this->input->post('file_size_' . $file),
                        "created_at" => $now,
                        "uploaded_by" => $this->login_user->id
                    );
                    $success = $this->General_files_model->save($data);
                } else {
                    $success = false;
                }
            }
        }


        if ($success) {
            echo json_encode(array("success" => true, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    /* list of files, prepared for datatable  */

    function files_list_data($client_id = 0) {
        $this->access_only_allowed_members();
        $this->can_access_this_lead($client_id);

        $options = array("client_id" => $client_id);
        $list_data = $this->General_files_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_file_row($data);
        }
        echo json_encode(array("data" => $result));
    }

    private function _make_file_row($data) {
        $file_icon = get_file_icon(strtolower(pathinfo($data->file_name, PATHINFO_EXTENSION)));

        $image_url = get_avatar($data->uploaded_by_user_image);
        $uploaded_by = "<span class='avatar avatar-xs mr10'><img src='$image_url' alt='...'></span> $data->uploaded_by_user_name";

        $uploaded_by = get_team_member_profile_link($data->uploaded_by, $uploaded_by);

        $description = "<div class='pull-left'>" .
                js_anchor(remove_file_prefix($data->file_name), array('title' => "", "data-toggle" => "app-modal", "data-sidebar" => "0", "data-url" => get_uri("leads/view_file/" . $data->id)));

        if ($data->description) {
            $description .= "<br /><span>" . $data->description . "</span></div>";
        } else {
            $description .= "</div>";
        }

        $options = anchor(get_uri("leads/download_file/" . $data->id), "<i class='fa fa fa-cloud-download'></i>", array("title" => lang("download")));

        $options .= js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_file'), "class" => "delete", "data-id" => $data->id, "data-action-url" => get_uri("leads/delete_file"), "data-action" => "delete-confirmation"));


        return array($data->id,
            "<div class='fa fa-$file_icon font-22 mr10 pull-left'></div>" . $description,
            convert_file_size($data->file_size),
            $uploaded_by,
            format_to_datetime($data->created_at),
            $options
        );
    }

    function view_file($file_id = 0) {
        $file_info = $this->General_files_model->get_details(array("id" => $file_id))->row();

        if ($file_info) {
            $this->access_only_allowed_members();

            if (!$file_info->client_id) {
                redirect("forbidden");
            }

            $this->can_access_this_lead($file_info->client_id);

            $view_data['can_comment_on_files'] = false;

            $file_url = get_source_url_of_file(make_array_of_file($file_info), get_general_file_path("client", $file_info->client_id));

            $view_data["file_url"] = $file_url;
            $view_data["is_image_file"] = is_image_file($file_info->file_name);
            $view_data["is_google_preview_available"] = is_google_preview_available($file_info->file_name);
            $view_data["is_viewable_video_file"] = is_viewable_video_file($file_info->file_name);
            $view_data["is_google_drive_file"] = ($file_info->file_id && $file_info->service_type == "google") ? true : false;

            $view_data["file_info"] = $file_info;
            $view_data['file_id'] = $file_id;
            $this->load->view("leads/files/view", $view_data);
        } else {
            show_404();
        }
    }

    /* download a file */

    function download_file($id) {

        $file_info = $this->General_files_model->get_one($id);

        if (!$file_info->client_id) {
            redirect("forbidden");
        }

        $this->can_access_this_lead($file_info->client_id);

        //serilize the path
        $file_data = serialize(array(make_array_of_file($file_info)));

        download_app_files(get_general_file_path("client", $file_info->client_id), $file_data);
    }

    /* upload a post file */

    function upload_file() {
        upload_file_to_temp();
    }

    /* check valid file for lead */

    function validate_file() {
        return validate_post_file($this->input->post("file_name"));
    }

    /* delete a file */

    function delete_file() {

        $id = $this->input->post('id');
        $info = $this->General_files_model->get_one($id);

        if (!$info->client_id) {
            redirect("forbidden");
        }

        $this->can_access_this_lead($info->client_id);

        if ($this->General_files_model->delete($id)) {

            //delete the files
            delete_app_files(get_general_file_path("client", $info->client_id), array(make_array_of_file($info)));

            echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
        }
    }

    function contact_profile($contact_id = 0, $tab = "") {
        $this->check_module_availability("module_lead");
        $this->access_only_allowed_members();

        $view_data['user_info'] = $this->Users_model->get_one($contact_id);
        $this->can_access_this_lead($view_data['user_info']->client_id);

        $view_data['lead_info'] = $this->Clients_model->get_one($view_data['user_info']->client_id);
        $view_data['tab'] = $tab;
        if ($view_data['user_info']->user_type === "lead") {

            $view_data['show_cotact_info'] = true;
            $view_data['show_social_links'] = true;
            $view_data['social_link'] = $this->Social_links_model->get_one($contact_id);
            $this->template->rander("leads/contacts/view", $view_data);
        } else {
            show_404();
        }
    }

    /* load contacts tab  */

    function contacts($client_id) {
        $this->access_only_allowed_members();

        if ($client_id) {
            $this->can_access_this_lead($client_id);
            $view_data['client_id'] = $client_id;
            $view_data["custom_field_headers"] = $this->Custom_fields_model->get_custom_field_headers_for_table("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type);

            $this->load->view("leads/contacts/index", $view_data);
        }
    }

    /* contact add modal */

    function add_new_contact_modal_form() {
        $this->access_only_allowed_members();

        $view_data['model_info'] = $this->Users_model->get_one(0);
        $view_data['model_info']->client_id = $this->input->post('client_id');
        $this->can_access_this_lead($view_data['model_info']->client_id);

        $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("lead_contacts", $view_data['model_info']->id, $this->login_user->is_admin, $this->login_user->user_type)->result();
        $this->load->view('leads/contacts/modal_form', $view_data);
    }

    /* load contact's general info tab view */

    function contact_general_info_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members();

            $view_data['model_info'] = $this->Users_model->get_one($contact_id);
            $this->can_access_this_lead($view_data['model_info']->client_id);
            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("lead_contacts", $contact_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";
            $this->load->view('leads/contacts/contact_general_info_tab', $view_data);
        }
    }

    /* load contact's company info tab view */

    function company_info_tab($client_id = 0) {
        if ($client_id) {
            $this->access_only_allowed_members();
            $this->can_access_this_lead($client_id);

            $view_data['model_info'] = $this->Clients_model->get_one($client_id);
            $view_data['statuses'] = $this->Lead_status_model->get_details()->result();
            $view_data['sources'] = $this->Lead_source_model->get_details()->result();

            $view_data["custom_fields"] = $this->Custom_fields_model->get_combined_details("leads", $client_id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $view_data['label_column'] = "col-md-2";
            $view_data['field_column'] = "col-md-10";

            $view_data["owners_dropdown"] = $this->_get_owners_dropdown();

            $this->load->view('leads/contacts/company_info_tab', $view_data);
        }
    }

    /* load contact's social links tab view */

    function contact_social_links_tab($contact_id = 0) {
        if ($contact_id) {
            $this->access_only_allowed_members();

            $view_data['user_id'] = $contact_id;
            $view_data['user_type'] = "lead";
            $view_data['model_info'] = $this->Social_links_model->get_one($contact_id);
            $this->load->view('users/social_links', $view_data);
        }
    }

    /* insert/upadate a contact */

    function save_contact() {
        $contact_id = $this->input->post('contact_id');
        $client_id = $this->input->post('client_id');

        $this->access_only_allowed_members();
        $this->can_access_this_lead($client_id);

        $user_data = array(
            "first_name" => $this->input->post('first_name'),
            "last_name" => $this->input->post('last_name'),
            "phone" => $this->input->post('phone'),
            "skype" => $this->input->post('skype'),
            "job_title" => $this->input->post('job_title'),
            "gender" => $this->input->post('gender'),
            "note" => $this->input->post('note'),
            "user_type" => "lead"
        );

        validate_submitted_data(array(
            "first_name" => "required",
            "last_name" => "required",
            "client_id" => "required|numeric",
            "email" => "required|valid_email"
        ));

        $user_data["email"] = trim($this->input->post('email'));

        //validate duplicate email address
        if ($this->Users_model->is_email_exists($user_data["email"])) {
            echo json_encode(array("success" => false, 'message' => lang('duplicate_email')));
            exit();
        }

        if (!$contact_id) {
            //inserting new contact. client_id is required
            //we'll save following fields only when creating a new contact from this form
            $user_data["client_id"] = $client_id;
            $user_data["created_at"] = get_current_utc_time();
        }

        //by default, the first contact of a lead is the primary contact
        //check existing primary contact. if not found then set the first contact = primary contact
        $primary_contact = $this->Clients_model->get_primary_contact($client_id);
        if (!$primary_contact) {
            $user_data['is_primary_contact'] = 1;
        }

        //only admin can change existing primary contact
        $is_primary_contact = $this->input->post('is_primary_contact');
        if ($is_primary_contact && $this->login_user->is_admin) {
            $user_data['is_primary_contact'] = 1;
        }

        $user_data = clean_data($user_data);

        $save_id = $this->Users_model->save($user_data, $contact_id);
        if ($save_id) {

            save_custom_fields("lead_contacts", $save_id, $this->login_user->is_admin, $this->login_user->user_type);

            //has changed the existing primary contact? updete previous primary contact and set is_primary_contact=0
            if ($is_primary_contact) {
                $user_data = array("is_primary_contact" => 0);
                $this->Users_model->save($user_data, $primary_contact);
            }

            echo json_encode(array("success" => true, "data" => $this->_contact_row_data($save_id), 'id' => $contact_id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    //save social links of a contact
    function save_contact_social_links($contact_id = 0) {
        $this->access_only_allowed_members();

        $lead_info = $this->Users_model->get_one($contact_id);
        $this->can_access_this_lead($lead_info->client_id);

        $id = 0;

        //find out, the user has existing social link row or not? if found update the row otherwise add new row.
        $has_social_links = $this->Social_links_model->get_one($contact_id);
        if (isset($has_social_links->id)) {
            $id = $has_social_links->id;
        }

        $social_link_data = array(
            "facebook" => $this->input->post('facebook'),
            "twitter" => $this->input->post('twitter'),
            "linkedin" => $this->input->post('linkedin'),
            "googleplus" => $this->input->post('googleplus'),
            "digg" => $this->input->post('digg'),
            "youtube" => $this->input->post('youtube'),
            "pinterest" => $this->input->post('pinterest'),
            "instagram" => $this->input->post('instagram'),
            "github" => $this->input->post('github'),
            "tumblr" => $this->input->post('tumblr'),
            "vine" => $this->input->post('vine'),
            "user_id" => $contact_id,
            "id" => $id ? $id : $contact_id
        );

        $social_link_data = clean_data($social_link_data);

        $this->Social_links_model->save($social_link_data, $id);
        echo json_encode(array("success" => true, 'message' => lang('record_updated')));
    }

    //save profile image of a contact
    function save_profile_image($user_id = 0) {
        $this->access_only_allowed_members();
        $lead_info = $this->Users_model->get_one($user_id);
        $this->can_access_this_lead($lead_info->client_id);

        //process the the file which has uploaded by dropzone
        $profile_image = str_replace("~", ":", $this->input->post("profile_image"));

        if ($profile_image) {
            $profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $profile_image));

            //delete old file
            delete_app_files(get_setting("profile_image_path"), array(@unserialize($lead_info->image)));

            $image_data = array("image" => $profile_image);
            $this->Users_model->save($image_data, $user_id);
            echo json_encode(array("success" => true, 'message' => lang('profile_image_changed')));
        }

        //process the the file which has uploaded using manual file submit
        if ($_FILES) {
            $profile_image_file = get_array_value($_FILES, "profile_image_file");
            $image_file_name = get_array_value($profile_image_file, "tmp_name");
            if ($image_file_name) {
                if (!$this->check_profile_image_dimension($image_file_name)) {
                    echo json_encode(array("success" => false, 'message' => lang('profile_image_error_message')));
                    exit();
                }
                
                $profile_image = serialize(move_temp_file("avatar.png", get_setting("profile_image_path"), "", $image_file_name));

                //delete old file
                delete_app_files(get_setting("profile_image_path"), array(@unserialize($lead_info->image)));

                $image_data = array("image" => $profile_image);
                $this->Users_model->save($image_data, $user_id);
                echo json_encode(array("success" => true, 'message' => lang('profile_image_changed'), "reload_page" => true));
            }
        }
    }

    /* delete or undo a contact */

    function delete_contact() {

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $this->access_only_allowed_members();

        $id = $this->input->post('id');

        $lead_info = $this->Users_model->get_one($id);
        $this->can_access_this_lead($lead_info->client_id);

        if ($this->input->post('undo')) {
            if ($this->Users_model->delete($id, true)) {
                echo json_encode(array("success" => true, "data" => $this->_contact_row_data($id), "message" => lang('record_undone')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        } else {
            if ($this->Users_model->delete($id)) {
                echo json_encode(array("success" => true, 'message' => lang('record_deleted')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('record_cannot_be_deleted')));
            }
        }
    }

    /* list of contacts, prepared for datatable  */

    function contacts_list_data($client_id = 0) {

        $this->access_only_allowed_members();
        $this->can_access_this_lead($client_id);

        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type);

        $options = array("user_type" => "lead", "client_id" => $client_id, "custom_fields" => $custom_fields);
        $list_data = $this->Users_model->get_details($options)->result();
        $result = array();
        foreach ($list_data as $data) {
            $result[] = $this->_make_contact_row($data, $custom_fields);
        }
        echo json_encode(array("data" => $result));
    }

    /* return a row of contact list table */

    private function _contact_row_data($id) {
        $custom_fields = $this->Custom_fields_model->get_available_fields_for_table("lead_contacts", $this->login_user->is_admin, $this->login_user->user_type);
        $options = array(
            "id" => $id,
            "user_type" => "lead",
            "custom_fields" => $custom_fields
        );
        $data = $this->Users_model->get_details($options)->row();
        return $this->_make_contact_row($data, $custom_fields);
    }

    /* prepare a row of contact list table */

    private function _make_contact_row($data, $custom_fields) {
        $image_url = get_avatar($data->image);
        $user_avatar = "<span class='avatar avatar-xs'><img src='$image_url' alt='...'></span>";
        $full_name = $data->first_name . " " . $data->last_name . " ";
        $primary_contact = "";
        if ($data->is_primary_contact == "1") {
            $primary_contact = "<span class='label-info label'>" . lang('primary_contact') . "</span>";
        }

        $contact_link = anchor(get_uri("leads/contact_profile/" . $data->id), $full_name . $primary_contact);
        if ($this->login_user->user_type === "lead") {
            $contact_link = $full_name; //don't show clickable link to lead
        }


        $row_data = array(
            $user_avatar,
            $contact_link,
            $data->job_title,
            $data->email,
            $data->phone ? $data->phone : "-",
            $data->skype ? $data->skype : "-"
        );

        foreach ($custom_fields as $field) {
            $cf_id = "cfv_" . $field->id;
            $row_data[] = $this->load->view("custom_fields/output_" . $field->field_type, array("value" => $data->$cf_id), true);
        }

        $row_data[] = js_anchor("<i class='fa fa-times fa-fw'></i>", array('title' => lang('delete_contact'), "class" => "delete", "data-id" => "$data->id", "data-action-url" => get_uri("leads/delete_contact"), "data-action" => "delete"));

        return $row_data;
    }

    /* upadate a lead status */

    function save_lead_status($id = 0) {
        $this->access_only_allowed_members();
        $this->can_access_this_lead($id);

        $data = array(
            "lead_status_id" => $this->input->post('value')
        );

        $save_id = $this->Clients_model->save($data, $id);

        if ($save_id) {
            echo json_encode(array("success" => true, "data" => $this->_row_data($save_id), 'id' => $save_id, "message" => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, lang('error_occurred')));
        }
    }

    function all_leads_kanban() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_lead");

        $view_data['owners_dropdown'] = $this->_get_owners_dropdown("filter");
        $view_data['lead_sources'] = $this->Lead_source_model->get_details()->result();

        $this->template->rander("leads/kanban/all_leads", $view_data);
    }

    function all_leads_kanban_data() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_lead");
        $show_own_leads_only_user_id = $this->show_own_leads_only_user_id();

        $options = array(
            "status" => $this->input->post('status'),
            "owner_id" => $show_own_leads_only_user_id ? $show_own_leads_only_user_id : $this->input->post('owner_id'),
            "source" => $this->input->post('source'),
            "search" => $this->input->post('search'),
        );

        $view_data["leads"] = $this->Clients_model->get_leads_kanban_details($options)->result();

        $statuses = $this->Lead_status_model->get_details();
        $view_data["total_columns"] = $statuses->num_rows();
        $view_data["columns"] = $statuses->result();

        $this->load->view('leads/kanban/kanban_view', $view_data);
    }

    function save_lead_sort_and_status() {
        $this->access_only_allowed_members();
        $this->check_module_availability("module_lead");

        validate_submitted_data(array(
            "id" => "required|numeric"
        ));

        $id = $this->input->post('id');
        $this->can_access_this_lead($id);

        $lead_status_id = $this->input->post('lead_status_id');
        $data = array(
            "sort" => $this->input->post('sort')
        );

        if ($lead_status_id) {
            $data["lead_status_id"] = $lead_status_id;
        }

        $this->Clients_model->save($data, $id);
    }

    function make_client_modal_form($lead_id = 0) {
        $this->access_only_allowed_members();
        $this->can_access_this_lead($lead_id);

        //prepare company details
        $view_data["lead_info"] = $this->make_lead_modal_form_data($lead_id);
        $view_data["lead_info"]["to_custom_field_type"] = "clients";

        //prepare contacts info
        $final_contacts = array();
        $contacts = $this->Users_model->get_all_where(array("user_type" => "lead", "deleted" => 0, "status" => "active", "client_id" => $lead_id))->result();

        //add custom fields for contacts
        foreach ($contacts as $contact) {
            $contact->custom_fields = $this->Custom_fields_model->get_combined_details("lead_contacts", $contact->id, $this->login_user->is_admin, $this->login_user->user_type)->result();

            $final_contacts[] = $contact;
        }

        $view_data["contacts"] = $final_contacts;

        $view_data["team_members_dropdown"] = $this->get_team_members_dropdown();

        $this->load->view('leads/migration/modal_form', $view_data);
    }

    function save_as_client() {
        $this->access_only_allowed_members();

        $client_id = $this->input->post('main_client_id');
        $this->can_access_this_lead($client_id);

        if ($client_id) {
            //save client info
            validate_submitted_data(array(
                "main_client_id" => "numeric",
                "company_name" => "required"
            ));

            $company_name = $this->input->post('company_name');

            $client_info = $this->Clients_model->get_details(array("id" => $client_id))->row();

            $data = array(
                "company_name" => $company_name,
                "address" => $this->input->post('address'),
                "city" => $this->input->post('city'),
                "state" => $this->input->post('state'),
                "zip" => $this->input->post('zip'),
                "country" => $this->input->post('country'),
                "phone" => $this->input->post('phone'),
                "website" => $this->input->post('website'),
                "vat_number" => $this->input->post('vat_number'),
                "group_ids" => $this->input->post('group_ids') ? $this->input->post('group_ids') : "",
                "is_lead" => 0,
                "client_migration_date" => get_current_utc_time(),
                "last_lead_status" => $client_info->lead_status_title,
                "created_by" => $this->input->post('created_by') ? $this->input->post('created_by') : $client_info->owner_id
            );

            if ($this->login_user->is_admin) {
                $data["currency_symbol"] = $this->input->post('currency_symbol') ? $this->input->post('currency_symbol') : "";
                $data["currency"] = $this->input->post('currency') ? $this->input->post('currency') : "";
                $data["disable_online_payment"] = $this->input->post('disable_online_payment') ? $this->input->post('disable_online_payment') : 0;
            }

            $data = clean_data($data);

            //check duplicate company name, if found then show an error message
            if (get_setting("disallow_duplicate_client_company_name") == "1" && $this->Clients_model->is_duplicate_company_name($company_name, $client_id)) {
                echo json_encode(array("success" => false, 'message' => lang("account_already_exists_for_your_company_name")));
                exit();
            }

            $save_client_id = $this->Clients_model->save($data, $client_id);

            //save contacts
            if ($save_client_id) {
                log_notification("client_created_from_lead", array("client_id" => $save_client_id), $this->login_user->id);

                //save custom field for client
                if ($this->input->post("merge_custom_fields-$client_id")) {
                    save_custom_fields("leads", $save_client_id, $this->login_user->is_admin, $this->login_user->user_type, 0, "clients");
                }

                $contacts = $this->Users_model->get_all_where(array("user_type" => "lead", "deleted" => 0, "status" => "active", "client_id" => $client_id))->result();
                $found_primary_contact = false;

                foreach ($contacts as $contact) {
                    validate_submitted_data(array(
                        'first_name-' . $contact->id => "required",
                        'last_name-' . $contact->id => "required",
                        'email-' . $contact->id => "required|valid_email"
                    ));

                    $user_data = array(
                        "first_name" => $this->input->post('first_name-' . $contact->id),
                        "last_name" => $this->input->post('last_name-' . $contact->id),
                        "phone" => $this->input->post('contact_phone-' . $contact->id),
                        "skype" => $this->input->post('skype-' . $contact->id),
                        "job_title" => $this->input->post('job_title-' . $contact->id),
                        "gender" => $this->input->post('gender-' . $contact->id),
                        "email" => trim($this->input->post('email-' . $contact->id)),
                        "password" => md5($this->input->post('login_password-' . $contact->id)),
                        "user_type" => "client"
                    );

                    if ($this->input->post('is_primary_contact_value-' . $contact->id) && !$found_primary_contact) {
                        $user_data["is_primary_contact"] = 1;
                        $found_primary_contact = true; //flag that, a primary contact found
                    } else {
                        $user_data["is_primary_contact"] = 0;
                    }

                    if ($this->Users_model->is_email_exists($user_data["email"], $contact->id)) {
                        echo json_encode(array("success" => false, 'message' => lang('duplicate_email')));
                        exit();
                    }

                    $user_data = clean_data($user_data);

                    $save_contact_id = $this->Users_model->save($user_data, $contact->id);

                    if ($save_contact_id) {
                        //save custom fields for client contacts
                        if ($this->input->post("merge_custom_fields-$contact->id")) {
                            save_custom_fields("lead_contacts", $save_contact_id, $this->login_user->is_admin, $this->login_user->user_type, 0, "client_contacts", $contact->id);
                        }

                        if ($this->input->post('email_login_details-' . $contact->id)) {
                            $email_template = $this->Email_templates_model->get_final_template("login_info");

                            $parser_data["SIGNATURE"] = $email_template->signature;
                            $parser_data["USER_FIRST_NAME"] = $user_data["first_name"];
                            $parser_data["USER_LAST_NAME"] = $user_data["last_name"];
                            $parser_data["USER_LOGIN_EMAIL"] = $user_data["email"];
                            $parser_data["USER_LOGIN_PASSWORD"] = $this->input->post('login_password-' . $contact->id);
                            $parser_data["DASHBOARD_URL"] = base_url();
                            $parser_data["LOGO_URL"] = get_logo_url();

                            $message = $this->parser->parse_string($email_template->message, $parser_data, TRUE);
                            send_app_mail($this->input->post('email-' . $contact->id), $email_template->subject, $message);
                        }
                    }
                }

                echo json_encode(array("success" => true, 'redirect_to' => get_uri("clients/view/$save_client_id"), "message" => lang('record_saved')));
            } else {
                echo json_encode(array("success" => false, lang('error_occurred')));
            }
        }
    }

    function upload_excel_file() {
        upload_file_to_temp(true);
    }

    function import_leads_modal_form() {
        $this->access_only_allowed_members();
        $this->load->view("leads/import_leads_modal_form");
    }

    private function _prepare_lead_data($data_row, $allowed_headers) {
        //prepare lead data
        $lead_data = array("is_lead" => 1);
        $lead_contact_data = array("user_type" => "lead", "is_primary_contact" => 1);
        $custom_field_values_array = array();

        foreach ($data_row as $row_data_key => $row_data_value) {
            if (!$row_data_value) {
                continue;
            }

            $header_key_value = get_array_value($allowed_headers, $row_data_key);
            if (strpos($header_key_value, 'cf') !== false) { //custom field
                $explode_header_key_value = explode("-", $header_key_value);
                $custom_field_id = get_array_value($explode_header_key_value, 1);
                $custom_field_values_array[$custom_field_id] = $row_data_value;
            } else if ($header_key_value == "contact_first_name") {
                $lead_contact_data["first_name"] = $row_data_value;
            } else if ($header_key_value == "contact_last_name") {
                $lead_contact_data["last_name"] = $row_data_value;
            } else if ($header_key_value == "contact_email") {
                $lead_contact_data["email"] = $row_data_value;
            } else if ($header_key_value == "status") {
                //get existing status, if not create new one and add the id
                $existing_status = $this->Lead_status_model->get_one_where(array("title" => $row_data_value, "deleted" => 0));
                if ($existing_status->id) {
                    $lead_data["lead_status_id"] = $existing_status->id;
                } else {
                    $max_sort_value = $this->Lead_status_model->get_max_sort_value();
                    $status_data = array("title" => $row_data_value, "color" => "#f1c40f", "sort" => ($max_sort_value * 1 + 1));
                    $lead_data["lead_status_id"] = $this->Lead_status_model->save($status_data);
                }
            } else if ($header_key_value == "source") {
                //get existing source, if not create new one and add the id
                $existing_source = $this->Lead_source_model->get_one_where(array("title" => $row_data_value, "deleted" => 0));
                if ($existing_source->id) {
                    $lead_data["lead_source_id"] = $existing_source->id;
                } else {
                    $max_sort_value = $this->Lead_source_model->get_max_sort_value();
                    $source_data = array("title" => $row_data_value, "sort" => ($max_sort_value * 1 + 1));
                    $lead_data["lead_source_id"] = $this->Lead_source_model->save($source_data);
                }
            } else {
                $lead_data[$header_key_value] = $row_data_value;
            }
        }

        return array(
            "lead_data" => $lead_data,
            "lead_contact_data" => $lead_contact_data,
            "custom_field_values_array" => $custom_field_values_array
        );
    }

    private function _get_existing_custom_field_id($title = "") {
        if (!$title) {
            return false;
        }

        $custom_field_data = array(
            "title" => $title,
            "related_to" => "leads"
        );

        $existing = $this->Custom_fields_model->get_one_where(array_merge($custom_field_data, array("deleted" => 0)));
        if ($existing->id) {
            return $existing->id;
        }
    }

    private function _prepare_headers_for_submit($headers_row, $headers) {
        foreach ($headers_row as $key => $header) {
            if (!((count($headers) - 1) < $key)) { //skip default headers
                continue;
            }

            //so, it's a custom field
            //check if there is any custom field existing with the title
            //add id like cf-3
            $existing_id = $this->_get_existing_custom_field_id($header);
            if ($existing_id) {
                array_push($headers, "cf-$existing_id");
            }
        }

        return $headers;
    }

    function save_lead_from_excel_file() {
        if (!$this->validate_import_leads_file_data(true)) {
            echo json_encode(array('success' => false, 'message' => lang('error_occurred')));
        }

        $file_name = $this->input->post('file_name');
        require_once(APPPATH . "third_party/php-excel-reader/SpreadsheetReader.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = new SpreadsheetReader($temp_file_path . $file_name);
        $allowed_headers = $this->_get_allowed_headers();
        $now = get_current_utc_time();

        foreach ($excel_file as $key => $value) { //rows
            if ($key === 0) { //first line is headers, modify this for custom fields and continue for the next loop
                $allowed_headers = $this->_prepare_headers_for_submit($value, $allowed_headers);
                continue;
            }

            $lead_data_array = $this->_prepare_lead_data($value, $allowed_headers);
            $lead_data = get_array_value($lead_data_array, "lead_data");
            $lead_contact_data = get_array_value($lead_data_array, "lead_contact_data");
            $custom_field_values_array = get_array_value($lead_data_array, "custom_field_values_array");

            //couldn't prepare valid data
            if (!($lead_data && count($lead_data) > 1)) {
                continue;
            }

            //found information about lead, add some additional info
            $lead_data["created_date"] = $now;
            $lead_data["owner_id"] = $this->login_user->id;
            $lead_contact_data["created_at"] = $now;

            //save lead data
            $lead_save_id = $this->Clients_model->save($lead_data);
            if (!$lead_save_id) {
                continue;
            }

            //save custom fields
            $this->_save_custom_fields_of_lead($lead_save_id, $custom_field_values_array);

            //add lead id to contact data
            $lead_contact_data["client_id"] = $lead_save_id;
            $this->Users_model->save($lead_contact_data);
        }

        delete_file_from_directory($temp_file_path . $file_name); //delete temp file

        echo json_encode(array('success' => true, 'message' => lang("record_saved")));
    }

    private function _save_custom_fields_of_lead($lead_id, $custom_field_values_array) {
        if (!$custom_field_values_array) {
            return false;
        }

        foreach ($custom_field_values_array as $key => $custom_field_value) {
            $field_value_data = array(
                "related_to_type" => "leads",
                "related_to_id" => $lead_id,
                "custom_field_id" => $key,
                "value" => $custom_field_value
            );

            $field_value_data = clean_data($field_value_data);

            $this->Custom_field_values_model->save($field_value_data);
        }
    }

    private function _get_allowed_headers() {
        return array(
            "company_name",
            "status",
            "source",
            "contact_first_name",
            "contact_last_name",
            "contact_email",
            "address",
            "city",
            "state",
            "zip",
            "country",
            "phone",
            "website",
            "vat_number",
            "currency",
            "currency_symbol"
        );
    }

    private function _store_headers_position($headers_row = array()) {
        $allowed_headers = $this->_get_allowed_headers();

        //check if all headers are correct and on the right position
        $final_headers = array();
        foreach ($headers_row as $key => $header) {
            $key_value = str_replace(' ', '_', strtolower($header));
            $header_on_this_position = get_array_value($allowed_headers, $key);
            $header_array = array("key_value" => $header_on_this_position, "value" => $header);

            if ($header_on_this_position == $key_value) {
                //allowed headers
                //the required headers should be on the correct positions
                //the rest headers will be treated as custom fields
                //pushed header at last of this loop
            } else if ((count($allowed_headers) - 1) < $key) {
                //custom fields headers
                //check if there is any existing custom field with this title
                if (!$this->_get_existing_custom_field_id($header)) {
                    $header_array["has_error"] = true;
                    $header_array["custom_field"] = true;
                }
            } else { //invalid header, flag as red
                $header_array["has_error"] = true;
            }

            array_push($final_headers, $header_array);
        }

        return $final_headers;
    }

    function validate_import_leads_file() {
        $file_name = $this->input->post("file_name");
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!is_valid_file_to_upload($file_name)) {
            echo json_encode(array("success" => false, 'message' => lang('invalid_file_type')));
            exit();
        }

        if ($file_ext == "xlsx") {
            echo json_encode(array("success" => true));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('please_upload_a_excel_file') . " (.xlsx)"));
        }
    }

    function validate_import_leads_file_data($check_on_submit = false) {
        $table_data = "";
        $error_message = "";
        $headers = array();
        $got_error_header = false; //we've to check the valid headers first, and a single header at a time
        $got_error_table_data = false;

        $file_name = $this->input->post("file_name");

        require_once(APPPATH . "third_party/php-excel-reader/SpreadsheetReader.php");

        $temp_file_path = get_setting("temp_file_path");
        $excel_file = new SpreadsheetReader($temp_file_path . $file_name);

        $table_data .= '<table class="table table-responsive table-bordered table-hover" style="width: 100%; color: #444;">';

        $table_data_header_array = array();
        $table_data_body_array = array();

        foreach ($excel_file as $row_key => $value) {
            if ($row_key == 0) { //validate headers
                $headers = $this->_store_headers_position($value);

                foreach ($headers as $row_data) {
                    $has_error_class = false;
                    if (get_array_value($row_data, "has_error") && !$got_error_header) {
                        $has_error_class = true;
                        $got_error_header = true;

                        if (get_array_value($row_data, "custom_field")) {
                            $error_message = lang("no_such_custom_field_found");
                        } else {
                            $error_message = sprintf(lang("import_client_error_header"), lang(get_array_value($row_data, "key_value")));
                        }
                    }

                    array_push($table_data_header_array, array("has_error_class" => $has_error_class, "value" => get_array_value($row_data, "value")));
                }
            } else { //validate data
                $error_message_on_this_row = "<ol class='pl15'>";
                $has_contact_first_name = get_array_value($value, 1) ? true : false;

                foreach ($value as $key => $row_data) {
                    $has_error_class = false;

                    if (!$got_error_header) {
                        $row_data_validation = $this->_row_data_validation_and_get_error_message($key, $row_data, $has_contact_first_name);
                        if ($row_data_validation) {
                            $has_error_class = true;
                            $error_message_on_this_row .= "<li>" . $row_data_validation . "</li>";
                            $got_error_table_data = true;
                        }
                    }

                    $table_data_body_array[$row_key][] = array("has_error_class" => $has_error_class, "value" => $row_data);
                }

                $error_message_on_this_row .= "</ol>";

                //error messages for this row
                if ($got_error_table_data) {
                    $table_data_body_array[$row_key][] = array("has_error_text" => true, "value" => $error_message_on_this_row);
                }
            }
        }

        //return false if any error found on submitting file
        if ($check_on_submit) {
            return ($got_error_header || $got_error_table_data) ? false : true;
        }

        //add error header if there is any error in table body
        if ($got_error_table_data) {
            array_push($table_data_header_array, array("has_error_text" => true, "value" => lang("error")));
        }

        //add headers to table
        $table_data .= "<tr>";
        foreach ($table_data_header_array as $table_data_header) {
            $error_class = get_array_value($table_data_header, "has_error_class") ? "error" : "";
            $error_text = get_array_value($table_data_header, "has_error_text") ? "text-danger" : "";
            $value = get_array_value($table_data_header, "value");
            $table_data .= "<th class='$error_class $error_text'>" . $value . "</th>";
        }
        $table_data .= "<tr>";

        //add body data to table
        foreach ($table_data_body_array as $table_data_body_row) {
            $table_data .= "<tr>";

            foreach ($table_data_body_row as $table_data_body_row_data) {
                $error_class = get_array_value($table_data_body_row_data, "has_error_class") ? "error" : "";
                $error_text = get_array_value($table_data_body_row_data, "has_error_text") ? "text-danger" : "";
                $value = get_array_value($table_data_body_row_data, "value");
                $table_data .= "<td class='$error_class $error_text'>" . $value . "</td>";
            }

            $table_data .= "<tr>";
        }

        //add error message for header
        if ($error_message) {
            $total_columns = count($table_data_header_array);
            $table_data .= "<tr><td class='text-danger' colspan='$total_columns'><i class='fa fa-warning'></i> " . $error_message . "</td></tr>";
        }

        $table_data .= "</table>";

        echo json_encode(array("success" => true, 'table_data' => $table_data, 'got_error' => ($got_error_header || $got_error_table_data) ? true : false));
    }

    private function _row_data_validation_and_get_error_message($key, $data, $has_contact_first_name) {
        $allowed_headers = $this->_get_allowed_headers();
        $header_value = get_array_value($allowed_headers, $key);

        //company name field is required
        if ($header_value == "company_name" && !$data) {
            return lang("import_client_error_company_name_field_required");
        }

        //if there is contact first name then the contact last name and email is required
        //the email should be unique then
        if ($has_contact_first_name) {
            if ($header_value == "contact_last_name" && !$data) {
                return lang("import_lead_error_contact_name");
            }
        }
    }

    function download_sample_excel_file() {
        $this->access_only_allowed_members();
        download_app_files(get_setting("system_file_path"), serialize(array(array("file_name" => "import-leads-sample.xlsx"))));
    }

}

/* End of file leads.php */
/* Location: ./application/controllers/leads.php */