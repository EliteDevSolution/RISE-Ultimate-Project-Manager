<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class External_tickets extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function index() { //embedded
        if (!get_setting("enable_embedded_form_to_get_tickets")) {
            show_404();
        }

        $view_data['topbar'] = false;
        $view_data['left_menu'] = false;

        $this->template->rander("external_tickets/index", $view_data);
    }

    private function is_valid_recaptcha($recaptcha_post_data) {
        //load recaptcha lib
        require_once(APPPATH . "third_party/recaptcha/autoload.php");
        $recaptcha = new \ReCaptcha\ReCaptcha(get_setting("re_captcha_secret_key"));
        $resp = $recaptcha->verify($recaptcha_post_data, $_SERVER['REMOTE_ADDR']);

        if ($resp->isSuccess()) {
            return true;
        } else {

            $error = "";
            foreach ($resp->getErrorCodes() as $code) {
                $error = $code;
            }

            return $error;
        }
    }

    //save external ticket
    function save() {
        if (!get_setting("enable_embedded_form_to_get_tickets")) {
            show_404();
        }

        validate_submitted_data(array(
            "title" => "required",
            "description" => "required",
            "email" => "required|valid_email"
        ));

        //check if there reCaptcha is enabled
        //if reCaptcha is enabled, check the validation
        if (get_setting("re_captcha_secret_key")) {

            $response = $this->is_valid_recaptcha($this->input->post("g-recaptcha-response"));

            if ($response !== true) {

                if ($response) {
                    echo json_encode(array('success' => false, 'message' => lang("re_captcha_error-" . $response)));
                } else {
                    echo json_encode(array('success' => false, 'message' => lang("re_captcha_expired")));
                }

                return false;
            }
        }

        $now = get_current_utc_time();

        $ticket_data = array(
            "title" => $this->input->post('title'),
            "created_at" => $now,
            "last_activity_at" => $now
        );

        //match with the existing client
        $email = $this->input->post('email');
        $contact_info = $this->Users_model->get_one_where(array("email" => $email, "user_type" => "client", "deleted" => 0));

        if ($contact_info->id) {
            //created by existing client
            $ticket_data["client_id"] = $contact_info->client_id;
            $ticket_data["created_by"] = $contact_info->id;
            $ticket_data["requested_by"] = $contact_info->id;
        } else {
            //unknown client
            $ticket_data["creator_email"] = $email;
            $ticket_data["client_id"] = 0;
            $ticket_data["created_by"] = 0;
            $ticket_data["requested_by"] = 0;
            $ticket_data["creator_name"] = $this->input->post('name') ? $this->input->post('name') : "";
        }

        $ticket_id = $this->Tickets_model->save($ticket_data);

        if ($ticket_id) {
            //save ticket's comment
            $comment_data = array(
                "description" => $this->input->post('description'),
                "ticket_id" => $ticket_id,
                "created_by" => $contact_info->id ? $contact_info->id : 0,
                "created_at" => $now
            );

            $comment_data = clean_data($comment_data);

            $target_path = get_setting("timeline_file_path");
            $comment_data["files"] = move_files_from_temp_dir_to_permanent_dir($target_path, "ticket");

            $ticket_comment_id = $this->Ticket_comments_model->save($comment_data);

            if ($ticket_id && $ticket_comment_id) {
                add_auto_reply_to_ticket($ticket_id);

                log_notification("ticket_created", array("ticket_id" => $ticket_id, "ticket_comment_id" => $ticket_comment_id, "exclude_ticket_creator" => true), $contact_info->id ? $contact_info->id : "0");

                echo json_encode(array("success" => true, 'message' => lang('ticket_submission_message')));

                return true;
            }
        }

        echo json_encode(array("success" => false, 'message' => lang('error_occurred')));
    }

    /* upload a file */

    function upload_file() {
        if (!get_setting("enable_embedded_form_to_get_tickets")) {
            show_404();
        }

        upload_file_to_temp();
    }

    /* check valid file for ticket */

    function validate_file() {
        if (!get_setting("enable_embedded_form_to_get_tickets")) {
            show_404();
        }

        return validate_post_file($this->input->post("file_name"));
    }

    function embedded_code_modal_form() {
        $embedded_code = "<iframe width='768' height='360' src='" . get_uri("external_tickets") . "' frameborder='0'></iframe>";
        $view_data['embedded'] = $embedded_code;

        $this->load->view('external_tickets/embedded_code_modal_form', $view_data);
    }

}

/* End of file External_tickets.php */
/* Location: ./application/controllers/External_tickets.php */