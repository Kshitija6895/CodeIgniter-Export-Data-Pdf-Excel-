    <?php
    if (!defined('BASEPATH'))
        exit('No direct script access allowed');
   
    require APPPATH . "/third_party/tcpdf/tcpdf.php";
    
    class Pdf extends tcpdf {
        public function __construct() {
            parent::__construct();
        }
    }
    ?>