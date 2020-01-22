    <?php
    /*
     * To change this license header, choose License Headers in Project Properties.
     * To change this template file, choose Tools | Templates
     * and open the template in the editor.
     */

    /**
     * Description of Createpdf Model: CodeIgniter Createpdf MySQL
     *
     * @author Kshitija Swami
     *
     * @email kshitijaswami@gmail.com
     */
    if (!defined('BASEPATH'))
        exit('No direct script access allowed');

    class Createpdf_model extends CI_Model {

        // get content 
        public function getContent($name) {        
           $query = $this->db->select('*')
                            ->from($name) 
                            ->get();
           return $query->result();
        }

         // Updates
        public function getUpdates($tableName,$p_id)
        {
            $dailySelect=$tableName.".date,".$tableName.".time,".$tableName.".description,projects.name as project name,users.name as user name";
            $eventStorySelect=$tableName.".date,".$tableName.".time,".$tableName.".description,projects.name as project name,".$tableName.".image,users.name as user name";
            $select=$tableName==="daily_update"?$dailySelect:$eventStorySelect;
            
            $Updates = $this->db->select( $select)
                                ->from($tableName)
                                ->where($tableName.".project_id=".$p_id)
                                ->join("projects","projects.project_id=".$tableName.".project_id")
                                ->join("users","users.user_id=".$tableName.".user_id")
                                ->get();
            return $Updates->result();
        }
    }
    ?>