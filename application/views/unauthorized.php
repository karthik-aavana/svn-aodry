<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('layout/header');
?>
<div class="content-wrapper">
    <section class="content-header">
        <h5>
            <ol class="breadcrumb">
                <li><a href="<?php echo base_url('auth/dashboard'); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                <li class="active">404 Entry</li>
            </ol>
        </h5>
    </section>
    <section class="content mt-50 unauthorized">
            <!-- <div class="brick"></div> -->
            <div class="number">
                <div class="four"></div>
                <div class="zero">
                    <div class="nail"></div>
                </div>
                <div class="four"></div>
            </div>
            <div class="info">
                <h2>Something is wrong</h2>
                <p>The page you are looking for was moved, removed, renamed or might never existed.</p>
                <a href="<?php echo base_url()?>auth/dashboard" class="btn">Go Home</a>
            </div>        
        <!-- <footer id="footer">
            <div class="container">
                <div class="worker"></div>
                <div class="tools"></div>
            </div>
        </footer>    -->     
    </section>
</div>
<script src="<?php echo base_url('assets/'); ?>js/jquery-3.3.1.min.js" type="text/javascript"></script>
<!-- <script src="<?php echo base_url('assets/'); ?>js/bootstrap.min.js" type="text/javascript"></script> -->
<script src="<?php echo base_url('assets/'); ?>js/modernizr.custom.js" type="text/javascript"></script>
<script src="<?php echo base_url('assets/'); ?>js/scripts.js" type="text/javascript"></script>
<?php
$this->load->view('layout/footer');
?>
