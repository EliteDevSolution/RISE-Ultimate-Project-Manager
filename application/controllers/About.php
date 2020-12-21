<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class About extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model("Pages_model");
    }

    function index($slug = "") {
        if (!$slug) {
            show_404();
        }

        $options = array("slug" => $slug);
        $page_info = $this->Pages_model->get_details($options)->row();

        if ($page_info->internal_use_only) {
            //the page should be visible on logged in user only
            $login_user_id = $this->Users_model->login_user_id();
            if (!$login_user_id) {
                show_404();
            }

            $user_info = $this->Users_model->get_one($login_user_id);

            if (!$user_info->is_admin && ($page_info->visible_to_team_members_only || $page_info->visible_to_clients_only)) {
                if ($page_info->visible_to_team_members_only && $user_info->user_type !== "staff") {
                    //the page should be visible to team members only
                    show_404();
                } else if ($page_info->visible_to_clients_only && $user_info->user_type !== "client") {
                    //the page should be visible to clients only
                    show_404();
                }
            }
        }

        $view_data["model_info"] = $page_info;

        $view_data['topbar'] = "includes/public/topbar";
        $view_data['left_menu'] = false;

        $this->template->rander("about/index", $view_data);
    }

}

/* End of file About.php */
/* Location: ./application/controllers/About.php */