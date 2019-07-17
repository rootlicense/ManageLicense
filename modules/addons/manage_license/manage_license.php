<?php
include_once(ROOTDIR . "/init.php");

if (!defined("DS")) {
	define('DS',DIRECTORY_SEPARATOR);
}

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Illuminate\Database\Capsule\Manager as DB;

require_once dirname(__FILE__) . "/extra/LicenseList.php";

function manage_license_config()
{
    return array(
        "name" => "Manage License",
        "description" => "for manage all product reserved",
        "version" => "1.0",
        "language" => "english",
        "author" => "<a class='btn btn-info btn-sm' href='https://nicsepehr.com/' target='_blank' >With<span class='glyphicon glyphicon-heart red '></span>NicSepehr Co</a> ",

    );

}

function manage_license_activate()
{
    return array('status' => 'success', 'description' => 'Addon has been successfully activated');
}

function manage_license_deactivate()
{
    # Return Result
    return array('status' => 'success', 'description' => 'Addon deactivated successfully.');
}

function manage_license_output(array $vars)
{
    $message = "";
    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'SolusVM') {
        License_List("SolusVM");
    } else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Whmcs') {
        License_List("Whmcs");
    } else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'LiteSpeed') {
        License_List("LiteSpeed");
    } else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'KernelCare') {
        License_List("KernelCare");
    } else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'CloudLinux') {
        License_List("CloudLinux");
    } else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Imunify360') {
        License_List("Imunify360");
    } else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'plesk') {
        License_List("plesk");
    } else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'cPanel') {
        License_List("cPanel");
    } else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'invoices') {
        Invoice_List();
    } else if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'userdetails') {
        userDetails();
    } else if (isset($_GET["action"]) && $_GET["action"] == "error") {
        header("location:?module=manage_license&show=alertUnSuccess");
    } else if (isset($_GET["action"]) && $_GET["action"] == "success") {
        header("location:?module=manage_license&show=alertSuccess");
    } else if (isset($_GET["action"]) && $_GET["action"] == "activeLicense") {
        activeLicense($_GET["id"]);
    } else if (isset($_GET["action"]) && $_GET["action"] == "deactivateLicense") {
        deactivateLicense($_GET["id"]);
    } else {
        $pArray = ["cPanel", "Plesk", "CloudLinux", "LiteSpeed", "SolusVM", "Whmcs"];
        $query = DB::table("tblproductconfiggroups")->get();

        $html = "";

        foreach ($query as $item) {
            $displayed = "none";
            $html .= "<div  id='col" . $item->id . "'>
                    <div class=\" dashboard-panel-item-columns-1\" '>";
            $html .= "<div class=\"panel panel-default widget-support\" data-widget=\"Support\">
        <div class=\"panel-heading\">
            
            <h3 class=\"panel-title\">$item->name</h3>
        </div>
           <div class=\"panel-body\" style=\"display: block;\">
           <div class=\"tickets-list\">";

            $query2 = DB::table("tblproductconfigoptions")->where("gid", $item->id)->get();
            foreach ($query2 as $item2) {

                $i = 1;
                if (in_array($item2->optionname, $pArray)) {
                    $displayed = "block";
                    //logActivity($item2->optionname);
                    if (DB::table("mod_manage_license_products")->where("name", "like", "$item2->optionname%")->exists()) {
                        $html .= "
             <div class=\"ticket\">
                    <div class=\"pull-right color-blue\">
                          <a href='?module=manage_license&action=deactivateLicense&id=" . $item2->id . "' class='btn btn-danger fieldlabel ' >Deactivate</a>
                    </div>
                    <span>$item2->optionname</span>
                </div>";
                    } else {
                        $html .= "
                    <div class=\"ticket\">
                    <div class=\"pull-right color-blue\">     
                       <a href='?module=manage_license&action=activeLicense&id=" . $item2->id . "' class='btn btn-primary ' >Active</a>
                     </div>
                    <span>$item2->optionname</span>
                    </div> ";
                    }
                } else {
                    if ($i == 1) {
                        $i++;
                        if ($displayed == "none") {
                            $html .= "<script>
                                        $('#col'+{$item->id}).remove();
                                        </script>";
                        }

                    }
                }
            }
            $html .= "</div></div>
        </div>
    </div>
</div>";
        }
        $output = "
    <div class='alert alert-danger alert-dismissible' role='alert' id='alertDiv'>
      <button type='button' class='close' data-dismiss='alert'><span aria-hidden='true'>&times;</span><span class='sr-only'>Close</span></button>
      <strong>ERROR!</strong> <span></span>
    </div>
    <div class='waitinf'>
    <i class='fas fa-sync fa-spin' ></i>
    </div>
    <ul class=\"nav nav-tabs admin-tabs\" role=\"tablist\">
    <li ><a class=\"tab-top\" href=\"#tab1\" role=\"tab\" data-toggle=\"tab\" id=\"tabLink1\" data-tab-id=\"1\" >product</a></li>
    <li><a class=\"tab-top\" href=\"#tab2\" role=\"tab\" data-toggle=\"tab\" id=\"tabLink5\" data-tab-id=\"5\">new product </a></li> 
    <li class=\"active\"><a class=\"tab-top\" href=\"#tab3\" role=\"tab\" data-toggle=\"tab\" id=\"tabLink3\" data-tab-id=\"3\" aria-expanded=\"false\">userProduct</a></li> 
    
    </ul>
<div class=\"tab-content admin-tabs\">
<li class=\"dropdown pull-right tabdrop hide\"><a class=\"dropdown-toggle\" data-toggle=\"dropdown\" href=\"#\"><i class=\"icon-align-justify\"></i> <b class=\"caret\"></b></a><ul class=\"dropdown-menu\"></ul></li>
<div class=\"tab-pane \" id=\"tab1\">
<div class='row'>
<div class='form col-sm-12 col-lg-12 col-md-6'>";

        if (isset($_GET["show"])) {
            if ($_GET["show"] == "alertSuccess") {
                $output .= "<div class=\"alert alert-success\" role=\"alert\">module active success</div>";
            } else if ($_GET["show" == "alertUnSuccess"]) {
                $output .= "<div class=\"alert alert-danger\" role=\"alert\">license not exist</div>";
            } else if ($_GET["show"] == "deleteSuccess") {
                $output .= "<div class=\"alert alert-success\" role=\"alert\">module delete success</div>";
            }
        }

        $output .= "$html
</div>
</div>

</div>


<div class=\"tab-pane \" id=\"tab2\">
<div class='row'>

<div class='form col-sm-12 col-lg-12 col-md-6'>";
        $output .= require "createProduct.php";

        $output .= "</div>
</div>

</div>
<div class=\"tab-pane active\" id=\"tab3\">
<div class='row'>
<div class='form col-sm-12 col-lg-12 col-md-6'>";
        $output .= include "userProduct.php";
        $output .= "</div></div>
</div>
</div>";
    }


    echo $output;
}

function manage_license_clientarea($vars)
{
    $language = strtolower($_SESSION['Language']);
    $lang = dirname(__FILE__) . DS . 'lang' . DS . $language . '.php';
//var_dump($lang);die;
    if (!file_exists($lang))
        $lang = dirname(__FILE__) . DS . 'lang' . DS . 'farsi.php';
    include($lang);
    $template = "template/";
    if (isset($_GET["action"])) {
        if (isset($_GET["id"])) {
            if ($_GET['action'] == "active") {
//                die(json_encode($vars));
                $res = DB::table("tblservers")->where('id', "17")->first();
                $res1 = DB::table("mod_manage_license_ap")->where("id", $_GET["id"])->first();
//                die(json_encode($res1->serviceid));
                if ($res && $res1) {
                    if ($res1->ip == "") {
                        $ip = DB::table("tblhosting")->where("id", $res1->serviceid)->select("dedicatedip")->first();
//                        die(json_encode( $ip));
                        DB::table("mod_manage_license_ap")
                            ->where("serviceid", $res1->serviceid)
                            ->where("userid", $res1->userid)->update([
                                'ip' => $ip->dedicatedip
                            ]);
                        $res1 = DB::table("mod_manage_license_ap")->where("id", $_GET["id"])->first();
//                        die(json_encode(  $res1->ip));
                    }
                    $vars["params"]["serverpassword"] = decryptPass($res->password);
                    $vars["params"]["serverusername"] = (string)$res->username;
                    $vars["params"]["serverhostname"] = (string)$res->hostname;
                    $vars["params"]["actions"] = "getLicenseInfo";
                    $vars["params"]["serviceid"] = $res1->serviceid;
                    $vars["params"]["userid"] = $res1->userid;
                    $vars["billing"] = $res1->billing;
                    $vars["params"]["serviceName"] = $res1->name;
                    $vars["params"]["customfields"]["IP"] = $res1->ip;
                    $vars["params"]['configoption1'] = $res1->name;
                    $vars["params"]["id"] = $res1->id;
//                    die(json_encode($vars["params"]));

                } else {

                    logActivity("error in password please contact with admin");
                    return array(
                        'templatefile' => $template . 'error',
                        'templateVariables' => array(
                            'errorMessage' => "access denied"
                        ),
                    );
                }
                $url = urlGenerate($vars["params"]);
//                die(json_encode($vars["params"]));
//                die( $url);
                $vars = setConfigOptions($vars);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($vars));
                $response = curl_exec($ch);
                if (curl_error($ch)) {
                    die('Unable to connect: ' . curl_errno($ch) . ' - ' . curl_error($ch));
                }

                curl_close($ch);
                $result = json_decode($response);
//                die(json_encode($response));
                if ($result->result == "success") {
                    $res = DB::table("mod_manage_license_ap")->where("id", $_GET["id"])
                        ->update([
                            "ip" => $result->response->ip,
                            "status" => "Active",
                            "licensekay" => $result->response->licensekey
                        ]);
                } elseif ($result->result == "error") {
//                    die(json_encode($result->message));
                    foreach ((array)$result->message as $item) {
                        $message = $item;
                    }
                    //logActivity($message);
                    return array(
                        'templatefile' => $template . 'error',
                        'templateVariables' => array(
                            'errorMessage' => $message
                        ),
                    );
                }
            }
        }
    }

    $res = DB::table("tblhosting")->where("tblhosting.userid", $_SESSION["uid"])
        ->select('tblhosting.billingcycle', 'tblhosting.domain', 'mod_manage_license_ap.*')
        ->join('mod_manage_license_ap', 'tblhosting.id', '=', 'mod_manage_license_ap.serviceid')
//        ->rawgroupBy("mod_manage_license_ap.serviceid")->get();
        ->groupBy('mod_manage_license_ap.serviceid')
        ->get();

    $output = "";
    if ($res) {
        foreach ($res as $re) {
//            die(json_encode($re));die;
            $output .= "<div class=\"dataTables_info\" style='margin-top: 25px;'>" . $_LANG["domain"] . "= $re->serviceid</div>";
            $output .= "<table class=\"table table-list dataTable no-footer dtr-inline\">
            <thead>
            <tr role=\"row\">
                <th>Product/Service</th>
                <th class=\"sorting\" rowspan=\"1\" colspan=\"1\">Billing</th>
                <th class=\"sorting\" rowspan=\"1\" colspan=\"1\">licenseKey  </th>
                <th class=\"sorting\" rowspan=\"1\" colspan=\"1\">  ip</th>
                <th class=\"sorting_asc\">Status</th>
            </tr></thead>";
            $res2 = DB::table("mod_manage_license_ap")->where("serviceid", $re->serviceid)->get();

            foreach ($res2 as $item) {

                $output .= "  <tbody><tr>
                    <td class=\"sorting_1 text-center\"><strong>$item->name </strong></td>
                    <td class=\"sorting_1 text-center\"><strong>$re->billingcycle </strong></td>
                    <td class=\"sorting_1 text-center\"><strong>$item->licensekay </strong></td>
                    <td class=\"sorting_1 text-center\"><strong>$item->ip </strong></td>
                    <td class=\"text-center\">
                       ";
                if (is_null($item->licensekay) && $item->status != "Active")
                    $output .= "<a href=\"?m=manage_license&action=active&id=$item->id\" class=\"btn btn-success \">Active</a>";
                else
                    $output .= "<span class=' alert-success'> Activated</span>";

                $output .= "</td>
                </tr></tbody>";
            }
            $output .= "</table>";

        }
    } else {

        return array(
            'templatefile' => $template . 'error',
            'templateVariables' => array(
                "errorMessge" => "no any license"
            ),


        );
    }
    $_SESSION["serviceid"] = $res[0]->serviceid;
    return array(
        'templatefile' => $template . 'home',
        'templateVariables' => array(
            'res' => $output,
            'session' => $_SESSION
        ),
    );

}


function activeLicense($id)
{
    $query = DB::table("tblproductconfigoptions")->where("id", $id)
        ->first();
    switch ($query->optionname) {
        case 'cPanel':
            $Product = ["None", "For Dedicate", "For VPS"];
            $query1 = DB::table("tblproductconfigoptionssub")
                ->where("configid", $id)->get();

            foreach ($query1 as $value) {
                if (in_array($value->optionname, $Product)) {
                    if ($value->optionname != "None") {
                        DB::table("mod_manage_license_products")
                            ->insert([
                                "option" => $query->id,
                                'suboption' => $value->id,
                                'name' => $query->optionname . '|' . $value->optionname]);

                    }
                } else {
                    header("location:?module=manage_license&action=error&message=$value->optionname license not exist in my license");
                    return;
                }
            }
            header('location:?module=manage_license&action=success&message=your license is active');
            break;
        case 'Plesk':
            $Product = [
                "None",
                "Host VPS",
                "Admin VPS",
                "Pro VPS",
                "Admin Dedicate",
                "Host Dedicate",
                "Pro Dedicate"
            ];
            $query1 = DB::table("tblproductconfigoptionssub")
                ->where("configid", $id)->get();

            foreach ($query1 as $value) {
                if (in_array($value->optionname, $Product)) {
                    if ($value->optionname != "None") {
                        DB::table("mod_manage_license_products")
                            ->insert([
                                "option" => $query->id,
                                'suboption' => $value->id,
                                'name' => $query->optionname . '|' . $value->optionname]);
                    }
                } else {
                    header("location:?module=manage_license&action=error&message=$value->optionname license not exist in my license");
                    return;
                }
            }
            header('location:?module=manage_license&action=success&message=your license is active');

            break;
        case SolusVM:
            $Product = [
                "None",
                "Enterprise",
                "Enterprise Slave Only",
                "SolusVM Slave for test"
            ];
            $query1 = DB::table("tblproductconfigoptionssub")
                ->where("configid", $id)->get();

            foreach ($query1 as $value) {
                if (in_array($value->optionname, $Product)) {
                    if ($value->optionname != "None") {
                        DB::table("mod_manage_license_products")
                            ->insert([
                                "option" => $query->id,
                                'suboption' => $value->id,
                                'name' => $query->optionname . '|' . $value->optionname]);
                    }
                } else {
                    header("location:?module=manage_license&action=error&message=$value->optionname license not exist in my license");
                    return;
                }
            }
            header('location:?module=manage_license&action=success&message=your license is active');

            break;
        case 'CloudLinux':
            $Product = [
                "None",
                "With cPanel",
                "Without cPanel",
                "Imunify360",
                "KernelCare",
                "CloudLinux",
            ];
            $query1 = DB::table("tblproductconfigoptionssub")
                ->where("configid", $id)->get();

            foreach ($query1 as $value) {
                if (in_array($value->optionname, $Product)) {
                    if ($value->optionname != "None") {
                        DB::table("mod_manage_license_products")
                            ->insert([
                                "option" => $query->id,
                                'suboption' => $value->id,
                                'name' => $query->optionname . '|' . $value->optionname]);
                    }
                } else {
                    header("location:?module=manage_license&action=error&message=$value->optionname license not exist in my license");
                    return;
                }
            }
            header('location:?module=manage_license&action=success&message=your license is active');

            break;
        case 'Whmcs':
            $Product = [
                "None",
                "Starter",
                "Plus",
                "Professional",
                "Business",
            ];
            $query1 = DB::table("tblproductconfigoptionssub")
                ->where("configid", $id)->get();

            foreach ($query1 as $value) {
                if (in_array($value->optionname, $Product)) {
                    if ($value->optionname != "None") {
                        DB::table("mod_manage_license_products")
                            ->insert([
                                "option" => $query->id,
                                'suboption' => $value->id,
                                'name' => $query->optionname . '|' . $value->optionname]);
                    }
                } else {
                    header("location:?module=manage_license&action=error&message=$value->optionname license not exist in my license");
                    return;
                }
            }
            header('location:?module=manage_license&action=success&message=your license is active');

            break;
        case 'LiteSpeed':
            $Product = [
                "None",
                "Site Owner Plus",
                "Site Owner",
                "Web Host Lite",
                " Web Host Essential",
                "Web Host Professional",
                "Web Host Enterprise",
                "Web Host Elite",
            ];
            $query1 = DB::table("tblproductconfigoptionssub")
                ->where("configid", $id)->get();
            foreach ($query1 as $value) {
                if (in_array($value->optionname, $Product)) {
                    if ($value->optionname != "None") {
                        DB::table("mod_manage_license_products")
                            ->insert([
                                "option" => $query->id,

                                'suboption' => $value->id,
                                'name' => $query->optionname . '|' . $value->optionname]);
                    }
                } else {
                    header("location:?module=manage_license&action=error&message=$value->optionname license not exist in my license");
                    return;
                }
            }
            header('location:?module=manage_license&action=success&message=your license is active');
    }
}

function deactivateLicense($id)
{
    DB::table("mod_manage_license_products")->where("option", $id)->delete();
    header("location:?module=manage_license&show=deleteSuccess");
}

function activeAddon($id)
{
    $res = Capsule::table("mod_manage_license_settings")->where("key", "=", 'addon')->get();

    if (!$res) {
        Capsule::insert("INSERT INTO `mod_manage_license_settings` 
(`id`, `name`,`key`,`value`) VALUES (NULL, 'addon_auto' ,'addon','$id')");
    } else {
        Capsule::table('mod_manage_license_settings')
            ->where('key', 'addon')
            ->update([
                'value' => $id
            ]);
    }
}

function manage_license_sidebar()
{

//    $smarty = new Smarty();
//    $smarty->display(dirname(__FILE__) . "/template/sidebar.tpl");
//
//    $code = "<div class='container'> <nav class='navbar navbar-default'>";
//    $code = " <div class='list-group'><span style='text-decoration: none;' class='list-group-item active glyphicon glyphicon-save'>Manage Licenses </span>";
//    if (empty($_GET['action']))
//        $code .= "<a style='text-decoration: none;' class='list-group-item  list-group-item-success active' href='addonmodules.php?module=manage_license&action=SolusVM'>SolusVM</a>";
//    else
//        $code .= "<a style='text-decoration: none;' class='list-group-item' href='addonmodules.php?module=manage_license'>SolusVM</a>";
//
//    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Whmcs')
//        $code .= "<a style='text-decoration:none;' class='list-group-item  list-group-item-success active' href='addonmodules.php?module=manage_license&action=Whmcs'>WHMCS</a> ";
//    else
//        $code .= "<a style='text-decoration:none;' class='list-group-item' href='addonmodules.php?module=manage_license&action=Whmcs'>WHMCS</a> ";
//
//    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'LiteSpeed')
//        $code .= "<a style='text-decoration:none;' class='list-group-item  list-group-item-success active' href='addonmodules.php?module=manage_license&action=LiteSpeed'>Litespeed</a> ";
//    else
//        $code .= "<a style='text-decoration:none;' class='list-group-item' href='addonmodules.php?module=manage_license&action=LiteSpeed'>Litespeed</a> ";
//
//    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'CloudLinux')
//        $code .= "<a style='text-decoration:none;' class='list-group-item  list-group-item-success active' href='addonmodules.php?module=manage_license&action=CloudLinux'>CloudLinux</a> ";
//    else
//        $code .= "<a style='text-decoration:none;' class='list-group-item' href='addonmodules.php?module=manage_license&action=CloudLinux'>CloudLinux</a> ";
//
//    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'KernelCare')
//        $code .= "<a style='text-decoration:none;' class='list-group-item  list-group-item-success active' href='addonmodules.php?module=manage_license&action=KernelCare'>KernelCare</a> ";
//    else
//        $code .= "<a style='text-decoration:none;' class='list-group-item' href='addonmodules.php?module=manage_license&action=KernelCare'>KernelCare</a> ";
//
//    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'Imunify360')
//        $code .= "<a style='text-decoration:none;' class='list-group-item  list-group-item-success active' href='addonmodules.php?module=manage_license&action=Imunify360'>Imunify360</a> ";
//    else
//        $code .= "<a style='text-decoration:none;' class='list-group-item' href='addonmodules.php?module=manage_license&action=Imunify360'>Imunify360</a> ";
//
//    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'cPanel')
//        $code .= "<a style='text-decoration:none;' class='list-group-item  list-group-item-success active' href='addonmodules.php?module=manage_license&action=cPanel'>cPanel</a> ";
//    else
//        $code .= "<a style='text-decoration:none;' class='list-group-item' href='addonmodules.php?module=manage_license&action=cPanel'>cPanel</a> ";
//
//    if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'invoices')
//        $code .= "<a style='text-decoration:none;' class='list-group-item  list-group-item-success active' href='addonmodules.php?module=manage_license&action=invoices'>invoices</a> ";
//    else
//        $code .= "<a style='text-decoration:none;' class='list-group-item' href='addonmodules.php?module=manage_license&action=invoices'>invoices</a> ";
//
//    $code .= "  </div>";
//
    $code = '

    
        <div class="panel-group" id="accordion">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><span
                                    class="glyphicon glyphicon-folder-close">
</span>Main</a>
                    </h4>
                </div>
                <div id="collapseOne" class="panel-collapse collapse in">
                    <div class="panel-body">
                        <table class="table table-hover table-striped">
                            <tr>
                                <td>
                                    <span class="glyphicon glyphicon-pencil text-primary"></span><a
                                            href="addonmodules.php?module=manage_license">Addon Home</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="glyphicon glyphicon-flash text-success"></span><a
                                            href="addonmodules.php?module=manage_license&action=invoices">Invoices</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <span class="glyphicon glyphicon-flash text-success"></span><a
                                            href="addonmodules.php?module=manage_license&action=userdetails">User Details</a>
                                </td>
                            </tr>
                          
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapseTwo"><span
                                    class="glyphicon glyphicon-th">
                            </span>Licenses List</a>
                    </h4>
                </div>
                <div id="collapseTwo" class="panel-collapse collapse">
                    <div class="panel-body">
                        <table class="table">
                            <tr>
                                <td>
                                    <a href="addonmodules.php?module=manage_license&action=SolusVM">SolusmVM</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="addonmodules.php?module=manage_license&action=Whmcs">WHMCS</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="addonmodules.php?module=manage_license&action=LiteSpeed">LiteSpeed</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="addonmodules.php?module=manage_license&action=CloudLinux">CloudLinux</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="addonmodules.php?module=manage_license&action=Imunify360">Imunify360</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="addonmodules.php?module=manage_license&action=KernelCare">KernelCare</a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <a href="addonmodules.php?module=manage_license&action=cPanel">cPanel</a>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
</div>';
    return $code;
}

function decryptPass(string $password)
{
    $res = DB::table("tbladmins")->where("roleid", "1")->first();
    $command = 'DecryptPassword';
    $postData = array(
        'password2' => $password,
    );
    $adminUsername = (string)$res->username; // Optional for WHMCS 7.2 and later

    $results = localAPI($command, $postData, $adminUsername);
    if ($results["result"] != "success") {
        logActivity("error in ganarete" . $results["message"]);
        return false;
    }
    return $results["password"];
}

function urlGenerate(array $params)
{
    $url = $params['serverhostname'];
    $type = 'addon';
    $action = $params['actions'];
    if ($params["serviceid"] == "9187") {
        $url = "http://nicsepehrapi.com/api/reseller/licenseha";
    }

    return $url . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $action;


}