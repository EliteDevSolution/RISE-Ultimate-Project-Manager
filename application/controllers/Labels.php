<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Labels extends MY_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() {
        redirect("forbidden");
    }

    private function can_access_labels_of_this_context($context = "", $label_id = 0) {
        if ($context == "project" && $this->can_edit_projects()) {
            return true;
        } else if ($context == "ticket") {
            $this->init_permission_checker("ticket");
            return $this->access_only_allowed_members();
        } else if ($context == "invoice") {
            $this->init_permission_checker("invoice");
            return $this->access_only_allowed_members();
        } else if ($context == "event" || $context == "note" || $context == "to_do") {
            if ($label_id) {
                //can access only own labels if there has any associated user id with this label
                $label_info = $this->Labels_model->get_one($label_id);
                if ($label_info->user_id && $label_info->user_id !== $this->login_user->id) {
                    return false;
                }
            }

            return true;
        } else if ($context == "task") {
            if ($this->can_manage_all_projects() || get_array_value($this->login_user->permissions, "can_edit_tasks") == "1") {
                return true;
            }
        }
    }

    function modal_form() {
        $type = $this->input->post("type");
        if (!$this->can_access_labels_of_this_context($type)) {
            redirect("forbidden");
        }

        if ($type) {
            $model_info = new stdClass();
            $model_info->color = "";

            $view_data["type"] = $type;
            $view_data["model_info"] = $model_info;

            $view_data["existing_labels"] = $this->_make_existing_labels_data($type);

            $this->load->view("labels/modal_form", $view_data);
        }
    }

    private function _make_existing_labels_data($type) {
        $labels_dom = "";
        $labels_where = array(
            "context" => $type
        );

        if ($type == "event" || $type == "note" || $type == "to_do") {
            $labels_where["user_id"] = $this->login_user->id;
        }

        $labels = $this->Labels_model->get_details($labels_where)->result();

        foreach ($labels as $label) {
            $labels_dom .= $this->_get_labels_row_data($label);
        }

        return $labels_dom;
    }

    private function _get_labels_row_data($data) {
        return "<span data-act='label-edit-delete' data-id='" . $data->id . "' data-color='" . $data->color . "' class='label large mr5 clickable' style='background-color: " . $data->color . "'>" . $data->title . "</span>";
    }

    function save() {
        validate_submitted_data(array(
            "id" => "numeric",
            "title" => "required",
            "type" => "required"
        ));

        $id = $this->input->post("id");
        $context = $this->input->post("type");

        if (!$this->can_access_labels_of_this_context($context, $id)) {
            redirect("forbidden");
        }

        $label_data = array(
            "context" => $context,
            "title" => $this->input->post("title"),
            "color" => $this->input->post("color")
        );

        //save user_id for only events and personal notes
        if ($context == "event" || $context == "to_do" || $context == "note") {
            $label_data["user_id"] = $this->login_user->id;
        }

        $save_id = $this->Labels_model->save($label_data, $id);

        if ($save_id) {
            $label_info = $this->Labels_model->get_one($save_id);
            echo json_encode(array("success" => true, 'data' => $this->_get_labels_row_data($label_info), 'id' => $id, 'message' => lang('record_saved')));
        } else {
            echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
        }
    }

    function delete() {
        $id = $this->input->post("id");
        $type = $this->input->post("type");

        if (!$this->can_access_labels_of_this_context($type, $id)) {
            redirect("forbidden");
        }

        if (!$id || !$type) {
            show_404();
        }

        $final_type = ($type == "to_do") ? $type : ($type . "s");
        $existing_labels = $this->Labels_model->is_label_exists($id, $final_type);

        if ($existing_labels) {
            echo json_encode(array("label_exists" => true, 'message' => lang("label_existing_error_message")));
        } else {
            if ($this->Labels_model->delete($id)) {
                echo json_encode(array("success" => true, 'id' => $id, 'message' => lang('record_saved')));
            } else {
                echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
            }
        }
    }

}

/* End of file Labels.php */
/* Location: ./application/controllers/Labels.php */