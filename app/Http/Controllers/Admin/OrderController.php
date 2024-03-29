<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderMaster;
use App\Models\OrderDetails;
use App\Models\OrderMasterBackup;
use App\Models\OrderDetailsBackup;
use App\Models\Settings;
use App\Models\SelfCollectionInfo;
use App\Models\EmailTemplate;
use App\Models\OrderDeliveryInfo;
use App\Models\OrderDeliveryDetails;
use App\Models\Country;
use DB;
use Session;
use App\Models\SMS;
use App\Models\ShippingMethods;
use App\Models\PaymentSettings;
use App\Models\PaymentMethods;
use Mail;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
		//$sortby = 'order_id';
        $sortby = 'date_entered';
        $sortorder = 'desc';
        $orderstatus = $request->order_status;

        $ordertype = $request->order_type;
        $orderfrom = $request->orderfrom;
        $orderto = $request->orderto;
        $orderstartno = $request->start_order_num;
        $orderendno = $request->end_order_num;
        $filtercolumn = $request->filter_column;
        $filter_srch_type = $request->filter_srch_type;
        $filter_srch_val = $request->filter_srch_val;
        $sortcolumn = $request->sort_column;
        $paymethod = $request->pay_method;
        $order_from = $request->order_from;

        //$join = 'order_type != 2 AND order_status > 0 ';
        $join = ' order_status > 0 ';

        if ($orderstatus != '') {
            if ($orderstatus == '2') {
                if ($join != '') {
                    $join .= ' AND (order_status = "' . $orderstatus . '" OR order_status = "3")';
                } else {
                    $join .= '(order_status = "' . $orderstatus . '" OR order_status = "3")';
                }
            } else {
                if ($join != '') {
                    $join .= ' AND order_status = "' . $orderstatus . '"';
                } else {
                    $join .= 'order_status = "' . $orderstatus . '"';
                }
            }
        }
        if ($ordertype != '') {
            if ($join != '') {
                $join .= ' AND order_type = "' . $ordertype . '"';
            } else {
                $join .= 'order_type = "' . $ordertype . '"';
            }
        }
        if ($orderstartno != '') {
            if ($join != '') {
                $join .= ' AND order_id >= "' . $orderstartno . '"';
            } else {
                $join .= 'order_id >= "' . $orderstartno . '"';
            }
        }
        if ($orderendno != '') {
            if ($join != '') {
                $join .= ' AND order_id <= "' . $orderendno . '"';
            } else {
                $join .= 'order_id <= "' . $orderendno . '"';
            }
        }
        if ($orderfrom != '') {
            if ($join != '') {
                $join .= ' AND DATE(date_entered) >= "' . $orderfrom . '"';
            } else {
                $join .= 'DATE(date_entered) >= "' . $orderfrom . '"';
            }
        }
        if ($orderto != '') {
            if ($join != '') {
                $join .= ' AND DATE(date_entered) <= "' . $orderto . '"';
            } else {
                $join .= 'DATE(date_entered) <= "' . $orderto . '"';
            }
        }

        if ($filter_srch_val != '') {
            if ($filtercolumn == 'cust_firstname') {
                if ($filter_srch_type == '1') {
                    if ($join != '') {
                        $join .= ' AND bill_fname = "' . $filter_srch_val . '"';
                    } else {
                        $join .= 'bill_fname = "' . $filter_srch_val . '"';
                    }
                } else {
                    if ($join != '') {
                        $join .= ' AND bill_fname LIKE "%' . $filter_srch_val . '%"';
                    } else {
                        $join .= 'bill_fname LIKE "%' . $filter_srch_val . '%"';
                    }
                }
            } elseif ($filtercolumn == 'cust_lastname') {
                if ($filter_srch_type == '1') {
                    if ($join != '') {
                        $join .= ' AND bill_lname = "' . $filter_srch_val . '"';
                    } else {
                        $join .= 'bill_lname = "' . $filter_srch_val . '"';
                    }
                } else {
                    if ($join != '') {
                        $join .= ' AND bill_lname LIKE "%' . $filter_srch_val . '%"';
                    } else {
                        $join .= 'bill_lname LIKE "%' . $filter_srch_val . '%"';
                    }
                }
            } elseif ($filtercolumn == 'cust_email') {
                if ($filter_srch_type == '1') {
                    if ($join != '') {
                        $join .= ' AND bill_email = "' . $filter_srch_val . '"';
                    } else {
                        $join .= 'bill_email = "' . $filter_srch_val . '"';
                    }
                } else {
                    if ($join != '') {
                        $join .= ' AND bill_email LIKE "%' . $filter_srch_val . '%"';
                    } else {
                        $join .= 'bill_email LIKE "%' . $filter_srch_val . '%"';
                    }
                }
            } elseif ($filtercolumn == 'cust_phone') {
                if ($filter_srch_type == '1') {
                    if ($join != '') {
                        $join .= ' AND bill_mobile = "' . $filter_srch_val . '"';
                    } else {
                        $join .= 'bill_mobile = "' . $filter_srch_val . '"';
                    }
                } else {
                    if ($join != '') {
                        $join .= ' AND bill_mobile LIKE "%' . $filter_srch_val . '%"';
                    } else {
                        $join .= 'bill_mobile LIKE "%' . $filter_srch_val . '%"';
                    }
                }
            }
        }
        if ($paymethod != '') {
            if ($join != '') {
                $join .= ' AND pay_method = "' . $paymethod . '"';
            } else {
                $join .= 'pay_method = "' . $paymethod . '"';
            }
        }
        if ($order_from != '') {
            if ($join != '') {
                $join .= ' AND order_from = "' . $order_from . '"';
            } else {
                $join .= 'order_from = "' . $order_from . '"';
            }
        }
        //echo $sortcolumn ;
        if ($sortcolumn > 0) {
            if ($sortcolumn == 1) { // order date asc
                $sortby = 'date_entered';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 2) { // order date desc
                $sortby = 'date_entered';
                $sortorder = 'desc';
            } elseif ($sortcolumn == 3) { // order date desc
                $sortby = 'bill_fname';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 4) { // order date desc
                $sortby = 'bill_fname';
                $sortorder = 'desc';
            } elseif ($sortcolumn == 5) { // order date desc
                $sortby = 'bill_lname';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 6) { // order date desc
                $sortby = 'bill_lname';
                $sortorder = 'desc';
            }
        }

        //echo $join;

        $orders = Order::where('order_id', '>', '0')->whereRaw($join)->orderBy($sortby, $sortorder)->paginate(100);

        $adminrole = 0;
        $moduleaccess = [];
        if (Session::has('accessrights')) {
            $moduleaccess = Session::get('accessrights');
        }

        if (Session::has('priority')) {
            $adminrole = Session::get('priority');
        }

        return view('admin/Order.index', compact('orders', 'orderstatus', 'ordertype', 'orderfrom', 'orderto', 'orderstartno', 'orderendno', 'filtercolumn', 'filter_srch_type', 'filter_srch_val', 'sortcolumn', 'moduleaccess', 'adminrole', 'paymethod', 'order_from'));
    }

    public function pendingorders(Request $request)
    {
        //$sortby = 'order_id';
        $sortby = 'date_entered';
        $sortorder = 'desc';
        $orderstatus = $request->order_status;
        $ordertype = $request->order_type;
        $orderfrom = $request->orderfrom;
        $orderto = $request->orderto;
        $orderstartno = $request->start_order_num;
        $orderendno = $request->end_order_num;
        $filtercolumn = $request->filter_column;
        $filter_srch_type = $request->filter_srch_type;
        $filter_srch_val = $request->filter_srch_val;
        $sortcolumn = $request->sort_column;

        $join = 'order_type > 0 AND order_status = 0 ';
        if ($orderstatus != '') {
            if ($join != '') {
                $join .= ' AND order_status = "' . $orderstatus . '"';
            } else {
                $join .= 'order_status = "' . $orderstatus . '"';
            }
        }
        if ($ordertype != '') {
            if ($join != '') {
                $join .= ' AND order_type = "' . $ordertype . '"';
            } else {
                $join .= 'order_type = "' . $ordertype . '"';
            }
        }
        if ($orderstartno != '') {
            if ($join != '') {
                $join .= ' AND order_id >= "' . $orderstartno . '"';
            } else {
                $join .= 'order_id >= "' . $orderstartno . '"';
            }
        }
        if ($orderendno != '') {
            if ($join != '') {
                $join .= ' AND order_id <= "' . $orderendno . '"';
            } else {
                $join .= 'order_id <= "' . $orderendno . '"';
            }
        }
        if ($orderfrom != '') {
            if ($join != '') {
                $join .= ' AND created_at >= "' . $orderfrom . '"';
            } else {
                $join .= 'created_at >= "' . $orderfrom . '"';
            }
        }
        if ($orderto != '') {
            if ($join != '') {
                $join .= ' AND created_at <= "' . $orderto . '"';
            } else {
                $join .= 'created_at <= "' . $orderto . '"';
            }
        }
        if ($filter_srch_val != '') {
            if ($filtercolumn == 'cust_firstname') {
                if ($filter_srch_type == '1') {
                    if ($join != '') {
                        $join .= ' AND bill_fname = "' . $filter_srch_val . '"';
                    } else {
                        $join .= 'bill_fname = "' . $filter_srch_val . '"';
                    }
                } else {
                    if ($join != '') {
                        $join .= ' AND bill_fname LIKE "%' . $filter_srch_val . '%"';
                    } else {
                        $join .= 'bill_fname LIKE "%' . $filter_srch_val . '%"';
                    }
                }
            } elseif ($filtercolumn == 'cust_lastname') {
                if ($filter_srch_type == '1') {
                    if ($join != '') {
                        $join .= ' AND bill_lname = "' . $filter_srch_val . '"';
                    } else {
                        $join .= 'bill_lname = "' . $filter_srch_val . '"';
                    }
                } else {
                    if ($join != '') {
                        $join .= ' AND bill_lname LIKE "%' . $filter_srch_val . '%"';
                    } else {
                        $join .= 'bill_lname LIKE "%' . $filter_srch_val . '%"';
                    }
                }
            }
        }
        if ($sortcolumn > 0) {
            if ($sortcolumn == 1) { // order date asc
                $sortby = 'date_entered';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 2) { // order date desc
                $sortby = 'date_entered';
                $sortorder = 'desc';
            } elseif ($sortcolumn == 3) { // order date desc
                $sortby = 'bill_fname';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 4) { // order date desc
                $sortby = 'bill_fname';
                $sortorder = 'desc';
            } elseif ($sortcolumn == 5) { // order date desc
                $sortby = 'bill_lname';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 6) { // order date desc
                $sortby = 'bill_lname';
                $sortorder = 'desc';
            }
        }

        $orders = Order::where('order_id', '>', '0')->whereRaw($join)->orderBy($sortby, $sortorder)->paginate(100);

        $adminrole = 0;
        $moduleaccess = [];
        if (Session::has('accessrights')) {
            $moduleaccess = Session::get('accessrights');
        }

        if (Session::has('priority')) {
            $adminrole = Session::get('priority');
        }

        return view('admin/Order.pendingorders', compact('orders', 'orderstatus', 'ordertype', 'orderfrom', 'orderto', 'orderstartno', 'orderendno', 'filtercolumn', 'filter_srch_type', 'filter_srch_val', 'sortcolumn', 'moduleaccess', 'adminrole'));
    }

    public function exportorders(Request $request)
    {
        $sortby = 'order_id';
        $sortorder = 'desc';
        $orderstatus = $request->order_status;
        $ordertype = $request->order_type;
        $orderfrom = $request->orderfrom;
        $orderto = $request->orderto;
        $orderstartno = $request->start_order_num;
        $orderendno = $request->end_order_num;
        $filtercolumn = $request->filter_column;
        $filter_srch_type = $request->filter_srch_type;
        $filter_srch_val = $request->filter_srch_val;
        $sortcolumn = $request->sort_column;

        $join = 'order_type > 0 AND order_status > 0 ';
        if ($orderstatus != '') {
            if ($join != '') {
                $join .= ' AND order_status = "' . $orderstatus . '"';
            } else {
                $join .= 'order_status = "' . $orderstatus . '"';
            }
        }
        if ($ordertype != '') {
            if ($join != '') {
                $join .= ' AND order_type = "' . $ordertype . '"';
            } else {
                $join .= 'order_type = "' . $ordertype . '"';
            }
        }
        if ($orderstartno != '') {
            if ($join != '') {
                $join .= ' AND order_id >= "' . $orderstartno . '"';
            } else {
                $join .= 'order_id >= "' . $orderstartno . '"';
            }
        }
        if ($orderendno != '') {
            if ($join != '') {
                $join .= ' AND order_id <= "' . $orderendno . '"';
            } else {
                $join .= 'order_id <= "' . $orderendno . '"';
            }
        }
        if ($orderfrom != '') {
            if ($join != '') {
                $join .= ' AND created_at >= "' . $orderfrom . '"';
            } else {
                $join .= 'created_at >= "' . $orderfrom . '"';
            }
        }
        if ($orderto != '') {
            if ($join != '') {
                $join .= ' AND created_at <= "' . $orderto . '"';
            } else {
                $join .= 'created_at <= "' . $orderto . '"';
            }
        }
        if ($filter_srch_val != '') {
            if ($filtercolumn == 'cust_firstname') {
                if ($filter_srch_type == '1') {
                    if ($join != '') {
                        $join .= ' AND bill_fname = "' . $filter_srch_val . '"';
                    } else {
                        $join .= 'bill_fname = "' . $filter_srch_val . '"';
                    }
                } else {
                    if ($join != '') {
                        $join .= ' AND bill_fname LIKE "%' . $filter_srch_val . '%"';
                    } else {
                        $join .= 'bill_fname LIKE "%' . $filter_srch_val . '%"';
                    }
                }
            } elseif ($filtercolumn == 'cust_lastname') {
                if ($filter_srch_type == '1') {
                    if ($join != '') {
                        $join .= ' AND bill_lname = "' . $filter_srch_val . '"';
                    } else {
                        $join .= 'bill_lname = "' . $filter_srch_val . '"';
                    }
                } else {
                    if ($join != '') {
                        $join .= ' AND bill_lname LIKE "%' . $filter_srch_val . '%"';
                    } else {
                        $join .= 'bill_lname LIKE "%' . $filter_srch_val . '%"';
                    }
                }
            }
        }
        if ($sortcolumn > 0) {
            if ($sortcolumn == 1) { // order date asc
                $sortby = 'created_at';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 2) { // order date desc
                $sortby = 'created_at';
                $sortorder = 'desc';
            } elseif ($sortcolumn == 3) { // order date desc
                $sortby = 'bill_fname';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 4) { // order date desc
                $sortby = 'bill_fname';
                $sortorder = 'desc';
            } elseif ($sortcolumn == 5) { // order date desc
                $sortby = 'bill_lname';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 6) { // order date desc
                $sortby = 'bill_lname';
                $sortorder = 'desc';
            }
        }
        $exportorders = Order::where('order_id', '>', '0')->whereRaw($join)->orderBy($sortby, $sortorder)->get();
        if ($exportorders) {
            $filename = 'orders_' . date("YmdHis") . '.csv';

            $fp = fopen('php://output', 'w');

            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename=' . $filename);

            $header = ['Order #', 'Date & Time', 'Payable Amount', 'Tax', 'Shipping Cost', 'Packaging Fee', 'Discount Amount', 'Shipping Method', 'Payment Method', 'Order Status', 'Billing First Name', 'Billing Last Name', 'Billing Address1', 'Billing Address2', 'Billing City', 'Billing State', 'Billing Country', 'Billing Zip', 'Billing Email', 'Shipping First Name', 'Shipping Last Name', 'Shipping Address1', 'Shipping Address2', 'Shipping City', 'Shipping State', 'Shipping Country', 'Shipping Zip', 'Shipping Email'];

            fputcsv($fp, $header);

            foreach ($exportorders as $exportorder) {
                $data = [];
                $exportorderid = $exportorder->order_id;
                if (strlen($exportorderid) == 3) {
                    $exportorderid = date('Ymd', strtotime($exportorder->created_at)) . "0" . $exportorderid;
                } elseif (strlen($exportorderid) == 2) {
                    $exportorderid = date('Ymd', strtotime($exportorder->created_at)) . "00" . $exportorderid;
                } elseif (strlen($exportorderid) == 1) {
                    $exportorderid = date('Ymd', strtotime($exportorder->created_at)) . "000" . $exportorderid;
                } else {
                    $exportorderid = date('Ymd', strtotime($exportorder->created_at));
                }

                $shipmethod = '';
                $orderstatus = 'Pending';
                $orderdate = date('d M Y H:i A', strtotime($exportorder->created_at));
                $shipping = ShippingMethods::where('Id', '=', $exportorder->ship_method)->first();
                if ($shipping) {
                    $shipmethod = $shipping->EnName;
                    if (strpos($shipmethod, 'Self Collection') !== false) {
                        $shipmethod = 'Self Collection';
                    }
                }

                if ($exportorder->order_status == 0) {
                    $orderstatus = 'Payment Pending';
                } elseif ($exportorder->order_status == 1) {
                    $orderstatus = 'Paid, Shipping Pending';
                } elseif ($exportorder->order_status == 2) {
                    $orderstatus = 'Shipped';
                } elseif ($exportorder->order_status == 3) {
                    $orderstatus = 'Shipped';
                } elseif ($exportorder->order_status == 5) {
                    $orderstatus = 'On the Way To You';
                } elseif ($exportorder->order_status == 6) {
                    $orderstatus = 'Partially Delivered';
                } elseif ($exportorder->order_status == 7) {
                    $orderstatus = 'Partially Refund';
                } elseif ($exportorder->order_status == 8) {
                    $orderstatus = 'Fully Refund';
                } elseif ($exportorder->order_status == 9) {
                    $orderstatus = 'Ready For Collection';
                }

                $data = [$exportorderid, $orderdate, $exportorder->payable_amount, $exportorder->tax_collected, $exportorder->shipping_cost, $exportorder->packaging_fee, $exportorder->discount_amount, $shipmethod, $exportorder->pay_method, $orderstatus, $exportorder->bill_fname, $exportorder->bill_lname, $exportorder->bill_ads1, $exportorder->bill_ads2, $exportorder->bill_city, $exportorder->bill_state, $exportorder->bill_country, $exportorder->bill_zip, $exportorder->bill_email, $exportorder->ship_fname, $exportorder->ship_lname, $exportorder->ship_ads1, $exportorder->ship_ads2, $exportorder->ship_city, $exportorder->ship_state, $exportorder->ship_country, $exportorder->ship_zip, $exportorder->ship_email];

                fputcsv($fp, $data);
            }

            fclose($fp);

            exit;
        }

    }

    public function exportpendingorders(Request $request)
    {
        $sortby = 'order_id';
        $sortorder = 'desc';
        $orderstatus = $request->order_status;
        $ordertype = $request->order_type;
        $orderfrom = $request->orderfrom;
        $orderto = $request->orderto;
        $orderstartno = $request->start_order_num;
        $orderendno = $request->end_order_num;
        $filtercolumn = $request->filter_column;
        $filter_srch_type = $request->filter_srch_type;
        $filter_srch_val = $request->filter_srch_val;
        $sortcolumn = $request->sort_column;

        $join = 'order_type > 0 AND order_status = 0 ';
        if ($orderstatus != '') {
            if ($join != '') {
                $join .= ' AND order_status = "' . $orderstatus . '"';
            } else {
                $join .= 'order_status = "' . $orderstatus . '"';
            }
        }
        if ($ordertype != '') {
            if ($join != '') {
                $join .= ' AND order_type = "' . $ordertype . '"';
            } else {
                $join .= 'order_type = "' . $ordertype . '"';
            }
        }
        if ($orderstartno != '') {
            if ($join != '') {
                $join .= ' AND order_id >= "' . $orderstartno . '"';
            } else {
                $join .= 'order_id >= "' . $orderstartno . '"';
            }
        }
        if ($orderendno != '') {
            if ($join != '') {
                $join .= ' AND order_id <= "' . $orderendno . '"';
            } else {
                $join .= 'order_id <= "' . $orderendno . '"';
            }
        }
        if ($orderfrom != '') {
            if ($join != '') {
                $join .= ' AND created_at >= "' . $orderfrom . '"';
            } else {
                $join .= 'created_at >= "' . $orderfrom . '"';
            }
        }
        if ($orderto != '') {
            if ($join != '') {
                $join .= ' AND created_at <= "' . $orderto . '"';
            } else {
                $join .= 'created_at <= "' . $orderto . '"';
            }
        }
        if ($filter_srch_val != '') {
            if ($filtercolumn == 'cust_firstname') {
                if ($filter_srch_type == '1') {
                    if ($join != '') {
                        $join .= ' AND bill_fname = "' . $filter_srch_val . '"';
                    } else {
                        $join .= 'bill_fname = "' . $filter_srch_val . '"';
                    }
                } else {
                    if ($join != '') {
                        $join .= ' AND bill_fname LIKE "%' . $filter_srch_val . '%"';
                    } else {
                        $join .= 'bill_fname LIKE "%' . $filter_srch_val . '%"';
                    }
                }
            } elseif ($filtercolumn == 'cust_lastname') {
                if ($filter_srch_type == '1') {
                    if ($join != '') {
                        $join .= ' AND bill_lname = "' . $filter_srch_val . '"';
                    } else {
                        $join .= 'bill_lname = "' . $filter_srch_val . '"';
                    }
                } else {
                    if ($join != '') {
                        $join .= ' AND bill_lname LIKE "%' . $filter_srch_val . '%"';
                    } else {
                        $join .= 'bill_lname LIKE "%' . $filter_srch_val . '%"';
                    }
                }
            }
        }
        if ($sortcolumn > 0) {
            if ($sortcolumn == 1) { // order date asc
                $sortby = 'created_at';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 2) { // order date desc
                $sortby = 'created_at';
                $sortorder = 'desc';
            } elseif ($sortcolumn == 3) { // order date desc
                $sortby = 'bill_fname';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 4) { // order date desc
                $sortby = 'bill_fname';
                $sortorder = 'desc';
            } elseif ($sortcolumn == 5) { // order date desc
                $sortby = 'bill_lname';
                $sortorder = 'asc';
            } elseif ($sortcolumn == 6) { // order date desc
                $sortby = 'bill_lname';
                $sortorder = 'desc';
            }
        }
        $exportorders = Order::where('order_id', '>', '0')->whereRaw($join)->orderBy($sortby, $sortorder)->get();
        if ($exportorders) {
            $filename = 'orders_' . date("YmdHis") . '.csv';

            $fp = fopen('php://output', 'w');

            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename=' . $filename);

            $header = ['Order #', 'Date & Time', 'Payable Amount', 'Tax', 'Shipping Cost', 'Packaging Fee', 'Discount Amount', 'Shipping Method', 'Payment Method', 'Order Status', 'Billing First Name', 'Billing Last Name', 'Billing Address1', 'Billing Address2', 'Billing City', 'Billing State', 'Billing Country', 'Billing Zip', 'Billing Email', 'Shipping First Name', 'Shipping Last Name', 'Shipping Address1', 'Shipping Address2', 'Shipping City', 'Shipping State', 'Shipping Country', 'Shipping Zip', 'Shipping Email'];

            fputcsv($fp, $header);

            foreach ($exportorders as $exportorder) {
                $data = [];
                $exportorderid = $exportorder->order_id;
                if (strlen($exportorderid) == 3) {
                    $exportorderid = date('Ymd', strtotime($exportorder->created_at)) . "0" . $exportorderid;
                } elseif (strlen($exportorderid) == 2) {
                    $exportorderid = date('Ymd', strtotime($exportorder->created_at)) . "00" . $exportorderid;
                } elseif (strlen($exportorderid) == 1) {
                    $exportorderid = date('Ymd', strtotime($exportorder->created_at)) . "000" . $exportorderid;
                } else {
                    $exportorderid = date('Ymd', strtotime($exportorder->created_at));
                }

                $shipmethod = '';
                $orderstatus = 'Pending';
                $orderdate = date('d M Y H:i A', strtotime($exportorder->created_at));
                $shipping = ShippingMethods::where('Id', '=', $exportorder->ship_method)->first();
                if ($shipping) {
                    $shipmethod = $shipping->EnName;
                    if (strpos($shipmethod, 'Self Collection') !== false) {
                        $shipmethod = 'Self Collection';
                    }
                }

                if ($exportorder->order_status == 0) {
                    $orderstatus = 'Payment Pending';
                } elseif ($exportorder->order_status == 1) {
                    $orderstatus = 'Paid, Shipping Pending';
                } elseif ($exportorder->order_status == 2) {
                    $orderstatus = 'Shipped';
                } elseif ($exportorder->order_status == 3) {
                    $orderstatus = 'Shipped';
                } elseif ($exportorder->order_status == 5) {
                    $orderstatus = 'On the Way To You';
                } elseif ($exportorder->order_status == 6) {
                    $orderstatus = 'Partially Delivered';
                } elseif ($exportorder->order_status == 7) {
                    $orderstatus = 'Partially Refund';
                } elseif ($exportorder->order_status == 8) {
                    $orderstatus = 'Fully Refund';
                } elseif ($exportorder->order_status == 9) {
                    $orderstatus = 'Ready For Collection';
                }

                $data = [$exportorderid, $orderdate, $exportorder->payable_amount, $exportorder->tax_collected, $exportorder->shipping_cost, $exportorder->packaging_fee, $exportorder->discount_amount, $shipmethod, $exportorder->pay_method, $orderstatus, $exportorder->bill_fname, $exportorder->bill_lname, $exportorder->bill_ads1, $exportorder->bill_ads2, $exportorder->bill_city, $exportorder->bill_state, $exportorder->bill_country, $exportorder->bill_zip, $exportorder->bill_email, $exportorder->ship_fname, $exportorder->ship_lname, $exportorder->ship_ads1, $exportorder->ship_ads2, $exportorder->ship_city, $exportorder->ship_state, $exportorder->ship_country, $exportorder->ship_zip, $exportorder->ship_email];

                fputcsv($fp, $data);
            }

            fclose($fp);

            exit;
        }

    }

    public function edit($id)
    {
        $order = Order::where('order_id', '=', $id)->first();
        return view('admin/Order.edit', compact('order'));
    }

    public function selfcollect($id)
    {
        $order = Order::where('order_id', '=', $id)->first();
        return view('admin/Order.selfcollect', compact('order'));
    }

    public function updateselfcollect(Request $request)
    {
        $selfcollection = new SelfCollectionInfo;
        $selfcollection->order_id = $request->orderid;
        $selfcollection->collection_status = $request->order_status;
        if (isset($request->colection_start_date)) {
            $selfcollection->colection_start_date = date('Y-m-d', strtotime($request->colection_start_date));
        } else {
            $selfcollection->colection_start_date = '';
        }
        $selfcollection->colection_start_time = $request->colection_start_time;
        if (isset($request->colection_to_date)) {
            $selfcollection->colection_to_date = date('Y-m-d', strtotime($request->colection_to_date));
        } else {
            $selfcollection->colection_to_date = '';
        }
        $selfcollection->colection_to_time = $request->colection_to_time;
        $selfcollection->remarks = $request->remarks;
        $selfcollection->save();

        OrderMaster::where('order_id', '=', $request->orderid)->update(array('order_status' => $request->order_status));

        $settings = Settings::where('id', '=', '1')->first();
        $adminemail = $settings->admin_email;
        $companyname = $settings->company_name;

        $replyto = $adminemail;

        $logo = url('/') . '/img/logo.png';
        $logo = '<img src="' . $logo . '">';

        $emailsubject = $emailcontent = '';
        $emailtemplate = EmailTemplate::where('template_type', '=', '12')->where('status', '=', '1')->first();
        if ($emailtemplate) {
            $emailsubject = $emailtemplate->subject;
            $emailcontent = $emailtemplate->content;

            $order = OrderMaster::where('order_id', '=', $request->orderid)->select('ship_fname', 'ship_lname', 'ship_email', 'created_at')->first();

            $custname = $order->ship_fname . ' ' . $order->ship_lname;
            $custemail = $order->ship_email;

            $orderid = $request->orderid;
            if (strlen($orderid) == 3) {
                $orderid = date('Ymd', strtotime($order->created_at)) . '0' . $orderid;
            } elseif (strlen($orderid) == 2) {
                $orderid = date('Ymd', strtotime($order->created_at)) . '00' . $orderid;
            } elseif (strlen($orderid) == 1) {
                $orderid = date('Ymd', strtotime($order->created_at)) . '000' . $orderid;
            } else {
                $orderid = date('Ymd', strtotime($order->created_at)) . $orderid;
            }

            $collectionfrom = $request->colection_start_date . ' ' . $request->colection_start_time;
            $collectionto = $request->colection_to_date . ' ' . $request->colection_to_time;

            $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
            $emailsubject = str_replace('{orderid}', $orderid, $emailsubject);
            $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
            $emailcontent = str_replace('{logo}', $logo, $emailcontent);
            $emailcontent = str_replace('{customername}', $custname, $emailcontent);
            $emailcontent = str_replace('{orderid}', $orderid, $emailcontent);
            $emailcontent = str_replace('{collectionfrom}', $collectionfrom, $emailcontent);
            $emailcontent = str_replace('{collectionto}', $collectionto, $emailcontent);
            $emailcontent = str_replace('{remarks}', $request->remarks, $emailcontent);

            $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
            $headers .= 'Reply-To: ' . $adminemail . "\r\n";
            $headers .= 'X-Mailer: PHP/' . phpversion();
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

            #@mail($custemail, $emailsubject, $emailcontent, $headers);
            Mail::send([], [], function ($message) use ($custemail, $emailsubject, $emailcontent) {
                $message->to($custemail)
                    ->subject($emailsubject)
                    ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                    ->setBody($emailcontent, 'text/html');
            });
        }

        return redirect('/admin/orders')->with('success', 'Order - Self Collection Successfully Updated!');
    }

    public function deletedorders()
    {
        $adminrole = 0;
        $moduleaccess = [];
        if (Session::has('accessrights')) {
            $moduleaccess = Session::get('accessrights');
        }

        if (Session::has('priority')) {
            $adminrole = Session::get('priority');
        }
        $orders = OrderMasterBackup::orderBy('order_id', 'desc')->paginate(100);
        return view('admin/Order.deletedorders', compact('orders', 'moduleaccess', 'adminrole'));
    }

    public function archiveorder($orderid)
    {
        $orders = OrderMasterBackup::where('order_id', $orderid)->first();
        $orderdetails = OrderDetailsBackup::where('order_id', '=', $orderid)->get();
        $settings = Settings::where('id', '=', '1')->first();
        $id = $orderid;
        return view('admin/Order.archiveorder', compact('orders', 'orderdetails', 'settings', 'id'));
    }

    public function bulkaction(Request $request)
    {

        $orderids = $request->orderids;
        $bulk_action = $request->bulk_action;
        $usermobile = 0;
        $status = 0;
        $statustext = $usercountry = '';
        $actualorderid = '';
        if ($bulk_action == 'single_delivery') {
            foreach ($orderids as $orderid) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('delivery_times' => 1));
            }
        } elseif ($bulk_action == 'multiple_delivery') {
            foreach ($orderids as $orderid) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('delivery_times' => 2));
            }
        } else {
            if ($bulk_action == 'on_the_way') {
                $status = 5;
                $statustext = 'On The Way';
            } elseif ($bulk_action == 'partial_delivered') {
                $status = 6;
                $statustext = 'Partially Delivered';
            } elseif ($bulk_action == 'partial_refunded') {
                $status = 7;
                $statustext = 'Partially Refunded';
            } elseif ($bulk_action == 'full_refunded') {
                $status = 8;
                $statustext = 'Fully Refunded';
            } elseif ($bulk_action == 'shipped') {
                $status = 3;
                $statustext = 'Shipped';
            }
            foreach ($orderids as $orderid) {

                OrderMaster::where('order_id', '=', $orderid)->update(array('order_status' => $status));

                if ($bulk_action == 'full_refunded') {
                    $orderdata = OrderMaster::where('order_id', '=', $orderid)->first();
                    if ($orderdata) {
                        if ($orderdata->pay_method == 'Atome') {
                            $currency = 'SGD';
                            $paysettings = PaymentSettings::where('id', '=', '1')->select('currency_type')->first();
                            if ($paysettings) {
                                $currency = $paysettings->currency_type;
                            }

                            $paymode = 'live';
                            $paymentmethod = PaymentMethods::where('id', '=', '7')->orWhere('payment_name', 'LIKE', '%Atome')->first();

                            if ($paymentmethod) {
                                $paymode = $paymentmethod->payment_mode;
                                if ($paymode == 'live') {
                                    $apikey = $paymentmethod->api_key;
                                    $apisignature = $paymentmethod->api_signature;
                                    $paymenturl = $paymentmethod->live_url;
                                } else {
                                    $apikey = $paymentmethod->test_api_key;
                                    $apisignature = $paymentmethod->test_api_signature;
                                    $paymenturl = $paymentmethod->testing_url;
                                }
                            }

                            $url = $paymenturl . "/auth";
                            $ch = curl_init($url);
                            $auth = base64_encode($apikey . ':' . $apisignature);
                            $header_str = "Authorization: Basic " . $auth;
                            $payload = json_encode(array("callbackUrl" => "https://hardwarecity.asia/atomecallback", "countryCode" => "SG"));
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                "Content-Type: application/json",
                                "Accept: application/json",
                                $header_str,
                            ));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $result = curl_exec($ch);
                            curl_close($ch);

                            if ($result) {
                                $response = json_decode($result);

                                print_r($response);

                                $authtoken = $response->code;

                                if ($authtoken == 'SUCCESS') {

                                    $url = $paymenturl . "/payments/" . $orderid . "/refund";

                                    $ch = curl_init($url);
                                    # Setup request to send json via POST.

                                    $payload = json_encode(array(
                                        "refundId" => $orderid,
                                        "refundAmount" => $orderdata->payable_amount * 100,
                                    ));

                                    $ch = curl_init($url);
                                    # Setup request to send json via POST.

                                    $auth = base64_encode($apikey . ':' . $apisignature);
                                    $header_str = "Authorization: Basic " . $auth;
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                                        "Content-Type: application/json",
                                        "Accept: application/json",
                                        $header_str,
                                    ));
                                    # Return response instead of printing.
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    # Send request.
                                    $result = curl_exec($ch);
                                    curl_close($ch);
                                }
                            }
                        }
                    }

                }

                $order = OrderMaster::where('order_id', '=', $orderid)->select('ship_fname', 'ship_lname', 'ship_email', 'ship_mobile', 'ship_country', 'created_at')->first();
                if ($order) {
                    $actualorderid = date('Ymd', strtotime($order->created_at)) . $orderid;
                    if (strlen($orderid) == 3) {
                        $actualorderid = date('Ymd', strtotime($order->created_at)) . "0" . $orderid;
                    } elseif (strlen($orderid) == 2) {
                        $actualorderid = date('Ymd', strtotime($order->created_at)) . "00" . $orderid;
                    } elseif (strlen($orderid) == 1) {
                        $actualorderid = date('Ymd', strtotime($order->created_at)) . "000" . $orderid;
                    }

                    $settings = Settings::where('id', '=', '1')->first();
                    $adminemail = $settings->admin_email;
                    $companyname = $settings->company_name;

                    $replyto = $adminemail;

                    $logo = url('/') . '/img/logo.png';
                    $logo = '<img src="' . $logo . '">';

                    $emailsubject = $emailcontent = '';
                    $emailtemplate = EmailTemplate::where('template_type', '=', '13')->where('status', '=', '1')->first();
                    if ($emailtemplate) {
                        $emailsubject = $emailtemplate->subject;
                        $emailcontent = $emailtemplate->content;

                        $custname = $order->ship_fname . ' ' . $order->ship_lname;
                        $custemail = $order->ship_email;

                        $invoiceurl = url('/') . '/invoice/' . $orderid;

                        $invoiceurl = '<a href="' . $invoiceurl . '" style="display: inline-block; border: none; font-size: 14px; font-weight: 600;   min-width: 80px; text-decoration: none; padding: 18px 47px 14px; text-transform: uppercase; background: #343a40; color: #fffb00; line-height: normal; cursor: pointer; text-align: center;">Click Here</a>';

                        $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                        $emailsubject = str_replace('{orderid}', $actualorderid, $emailsubject);
                        $emailsubject = str_replace('{status}', $statustext, $emailsubject);
                        $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                        $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                        $emailcontent = str_replace('{customername}', $custname, $emailcontent);
                        $emailcontent = str_replace('{orderid}', $actualorderid, $emailcontent);
                        $emailcontent = str_replace('{invoiceurl}', $invoiceurl, $emailcontent);

                        $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                        $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                        $headers .= 'X-Mailer: PHP/' . phpversion();
                        $headers .= "MIME-Version: 1.0\r\n";
                        $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                        #@mail($custemail, $emailsubject, $emailcontent, $headers);
                        Mail::send([], [], function ($message) use ($custemail, $emailsubject, $emailcontent) {
                            $message->to($custemail)
                                ->subject($emailsubject)
                                ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                                ->setBody($emailcontent, 'text/html');
                        });
                    }

                    /* Send SMS */
                    $usermobile = $order->ship_mobile;
                    $usercountry = $order->ship_country;
                    $sms = new \App\Models\SMS;
                    $sms->sendSMS($usermobile, $usercountry, $actualorderid, $statustext);
                }
            }
        }

        return redirect('/admin/orders')->with('success', 'Order Status Successfully Updated!');
    }

    public function pendingbulkaction(Request $request)
    {

        $orderids = $request->orderids;
        $bulk_action = $request->bulk_action;
        $usermobile = 0;
        $status = 0;
        $statustext = $usercountry = '';
        $actualorderid = '';
        if ($bulk_action == 'single_delivery') {
            foreach ($orderids as $orderid) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('delivery_times' => 1));
            }
        } elseif ($bulk_action == 'multiple_delivery') {
            foreach ($orderids as $orderid) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('delivery_times' => 2));
            }
        } elseif ($bulk_action == 'converted') {
            foreach ($orderids as $orderid) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('quotation_status' => 1));
            }
        } else {
            if ($bulk_action == 'on_the_way') {
                $status = 5;
                $statustext = 'On The Way';
            } elseif ($bulk_action == 'partial_delivered') {
                $status = 6;
                $statustext = 'Partially Delivered';
            } elseif ($bulk_action == 'partial_refunded') {
                $status = 7;
                $statustext = 'Partially Refunded';
            } elseif ($bulk_action == 'full_refunded') {
                $status = 8;
                $statustext = 'Fully Refunded';
            } elseif ($bulk_action == 'shipped') {
                $status = 3;
                $statustext = 'Shipped';
            }
            foreach ($orderids as $orderid) {
                OrderMaster::where('order_id', '=', $orderid)->update(array('order_status' => $status));
            }
        }

        return redirect('/admin/pendingorders')->with('success', 'Order Status Successfully Updated!');
    }

    public function singledelivery($id)
    {

        $deliverycount = OrderDeliveryInfo::where('order_id', $id)->count();
        if ($deliverycount > 0) {
            return redirect("/admin/orders/$id/deliverytrack");

        } else {

            $order = Order::where('order_id', '=', $id)->first();
            $countries = Country::where('country_status', '=', '1')->get();
            //$trackingid = rand(00000, 99999).$id;
            /*$deliverycount = OrderDeliveryInfo::where('order_id', $id)->count();
            $alphaarr = ['A', 'B', 'C', 'D', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
            if($deliverycount > 0) {
            $trackingid = $id.$alphaarr[$deliverycount-1];
            } else {
            $trackingid = $id;
            }*/

            $trackingid = $id;

            return view('admin/Order.singledelivery', compact('order', 'countries', 'trackingid'));
        }
    }

    public function updatesingledelivery(Request $request)
    {
        $authkey = '';
        $orderdeliveryinfo = new OrderDeliveryInfo;
        $orderdeliveryinfo->order_id = $request->orderid;
        $orderdeliveryinfo->shipping_by = $request->shipping_by;
        $tracking_number = $request->requested_tracking_number;
        $requesttracknumber = $request->requested_tracking_number;
        $statustext = '';
        if ($request->order_status == 2) {

            if ($request->shipping_by == 'Ninja Van') {
                $deliveryname = $deliveryemail = '';
                $order = OrderMaster::where('order_id', '=', $request->orderid)->select('ship_fname', 'ship_lname', 'ship_email')->first();
                if ($order) {
                    $deliveryname = $order->ship_fname . ' ' . $order->ship_lname;
                    $deliveryemail = $order->ship_email;
                }
                $ninja = new Order;
                if (Session::has('ninja_authkey')) {
                    $authkey = Session::get('ninja_authkey');
                } else {
                    $authkey = $ninja->getAuthKey();
                    Session::put('ninja_authkey', $authkey);
                }

                if ($authkey != '') {
                    $tracking_number = $ninja->getTrackingNumber($authkey, $requesttracknumber, $deliveryname, $deliveryemail, $request->ship_mobile, $request->ship_ads1, $request->ship_ads2, $request->ship_zip, $request->pickup_instruction, $request->delivery_instructions, $request->weight, $request->size);
                }

            }

            $orderdeliveryinfo->ship_tracking_number = $tracking_number;
            $orderdeliveryinfo->order_status = $request->order_status;
            $orderdeliveryinfo->status = $request->order_status;
            $orderdeliveryinfo->size = $request->size;
            $orderdeliveryinfo->weight = $request->weight;
            $orderdeliveryinfo->ship_ads1 = $request->ship_ads1;
            $orderdeliveryinfo->ship_ads2 = $request->ship_ads2;
            $orderdeliveryinfo->ship_country = $request->ship_country;
            $orderdeliveryinfo->ship_zip = $request->ship_zip;
            $orderdeliveryinfo->ship_mobile = $request->ship_mobile;
            $orderdeliveryinfo->pickup_instruction = $request->pickup_instruction;
            $orderdeliveryinfo->delivery_instructions = $request->delivery_instructions;
            $orderdeliveryinfo->shipment_appdt = date('Y-m-d H:i:s');
            $orderdeliveryinfo->delivered_date = date('Y-m-d');
            if ($request->next_delivery_date != '') {
                $orderdeliveryinfo->next_delivery_date = date('Y-m-d', strtotime($request->next_delivery_date));
            }
            $orderdeliveryinfo->save();

            $orderdeliveryinfoid = 0;
            $deliveryinfo = OrderDeliveryInfo::orderBy('delivery_id', 'desc')->select('delivery_id')->first();
            if ($deliveryinfo) {
                $orderdeliveryinfoid = $deliveryinfo->delivery_id;
            }

            $orderid = $request->orderid;

            $details = OrderDetails::where('order_id', '=', $orderid)->get();
            if ($details) {
                foreach ($details as $detail) {
                    $detailid = $detail->detail_id;
                    $orderdeliverydetails = new OrderDeliveryDetails;
                    $orderdeliverydetails->delivery_info_id = $orderdeliveryinfoid;
                    $orderdeliverydetails->order_id = $orderid;
                    $orderdeliverydetails->detail_id = $detailid;
                    $orderdeliverydetails->prod_id = $detail->prod_id;
                    $orderdeliverydetails->quantity = $detail->prod_quantity;
                    $orderdeliverydetails->status = 1;
                    $orderdeliverydetails->save();
                }
            }
        }

        OrderMaster::where('order_id', '=', $request->orderid)->update(array('ship_tracking_number' => $tracking_number, 'order_status' => $request->order_status));

        if ($request->order_status == 2) {
            $statustext = 'Shipped';
            $settings = Settings::where('id', '=', '1')->first();
            $adminemail = $settings->admin_email;
            $companyname = $settings->company_name;

            $replyto = $adminemail;

            $logo = url('/') . '/img/logo.png';
            $logo = '<img src="' . $logo . '">';

            $emailsubject = $emailcontent = '';
            $emailtemplate = EmailTemplate::where('template_type', '=', '13')->where('status', '=', '1')->first();
            if ($emailtemplate) {
                $emailsubject = $emailtemplate->subject;
                $emailcontent = $emailtemplate->content;

                $order = OrderMaster::where('order_id', '=', $request->orderid)->select('ship_fname', 'ship_lname', 'ship_email', 'created_at')->first();

                $custname = $order->ship_fname . ' ' . $order->ship_lname;
                $custemail = $order->ship_email;

                $orderid = $request->orderid;
                if (strlen($orderid) == 3) {
                    $orderid = date('Ymd', strtotime($order->created_at)) . '0' . $orderid;
                } elseif (strlen($orderid) == 2) {
                    $orderid = date('Ymd', strtotime($order->created_at)) . '00' . $orderid;
                } elseif (strlen($orderid) == 1) {
                    $orderid = date('Ymd', strtotime($order->created_at)) . '000' . $orderid;
                } else {
                    $orderid = date('Ymd', strtotime($order->created_at)) . $orderid;
                }

                $invoiceurl = url('/') . '/invoice/' . $request->orderid;

                $invoiceurl = '<a href="' . $invoiceurl . '" style="display: inline-block; border: none; font-size: 14px; font-weight: 600;   min-width: 80px; text-decoration: none; padding: 18px 47px 14px; text-transform: uppercase; background: #343a40; color: #fffb00; line-height: normal; cursor: pointer; text-align: center;">Click Here</a>';

                $emailsubject = str_replace('{companyname}', $companyname, $emailsubject);
                $emailsubject = str_replace('{orderid}', $orderid, $emailsubject);
                $emailsubject = str_replace('{status}', $statustext, $emailsubject);
                $emailcontent = str_replace('{companyname}', $companyname, $emailcontent);
                $emailcontent = str_replace('{logo}', $logo, $emailcontent);
                $emailcontent = str_replace('{customername}', $custname, $emailcontent);
                $emailcontent = str_replace('{orderid}', $orderid, $emailcontent);
                $emailcontent = str_replace('{invoiceurl}', $invoiceurl, $emailcontent);

                $headers = 'From: ' . $companyname . ' ' . $adminemail . '' . "\r\n";
                $headers .= 'Reply-To: ' . $adminemail . "\r\n";
                $headers .= 'X-Mailer: PHP/' . phpversion();
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";

                #@mail($custemail, $emailsubject, $emailcontent, $headers);
                Mail::send([], [], function ($message) use ($custemail, $emailsubject, $emailcontent) {
                    $message->to($custemail)
                        ->subject($emailsubject)
                        ->from(env('MAIL_USERNAME'), env('APP_NAME'))
                        ->setBody($emailcontent, 'text/html');
                });
            }
        }

        return redirect('/admin/orders/')->with('success', 'Order Status Successfully Updated!');

    }

    public function deliverytrack($id)
    {
        $order = Order::where('order_id', '=', $id)->first();
        $orderdetails = OrderDetails::where('order_id', '=', $id)->get();
        $orderdeliverydetails = OrderDeliveryDetails::where('order_id', '=', $id)->get();
        return view('admin/Order.deliverytrack', compact('order', 'orderdetails', 'orderdeliverydetails'));
    }

    public function deliveryinfo($id, $infoid)
    {
        $order = Order::where('order_id', '=', $id)->first();
        $countries = Country::where('country_status', '=', '1')->get();
        //$trackingid = rand(00000, 99999).$infoid;

        /*$deliverycount = OrderDeliveryInfo::where('order_id', $id)->count();
        $alphaarr = ['A', 'B', 'C', 'D', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
        if($deliverycount > 0) {
        $trackingid = $id.$alphaarr[$deliverycount-1];
        } else {
        $trackingid = $id;
        }*/

        $extra = $infoid;
        if (strlen($extra) >= 4) {
            $extra = $infoid;
        } elseif (strlen($extra) == 3) {
            $extra = '0' . $infoid;
        } elseif (strlen($extra) == 2) {
            $extra = '00' . $infoid;
        } elseif (strlen($extra) == 1) {
            $extra = '000' . $infoid;
        }

        $trackingid = $id . $extra;

        return view('admin/Order.deliveryinfo', compact('order', 'countries', 'trackingid', 'infoid'));
    }

    public function updatedeliveryinfo(Request $request)
    {

        $order_id = $request->orderid;
        $shipping_by = $request->shipping_by;
        $tracking_number = $request->requested_tracking_number;
        $requesttracknumber = $request->requested_tracking_number;

        if ($shipping_by == 'Ninja Van') {
            $deliveryname = $deliveryemail = '';
            $order = OrderMaster::where('order_id', '=', $request->orderid)->select('ship_fname', 'ship_lname', 'ship_email')->first();
            if ($order) {
                $deliveryname = $order->ship_fname . ' ' . $order->ship_lname;
                $deliveryemail = $order->ship_email;
            }
            $ninja = new Order;
            if (Session::has('ninja_authkey')) {
                $authkey = Session::get('ninja_authkey');
            } else {
                $authkey = $ninja->getAuthKey();
                Session::put('ninja_authkey', $authkey);
            }

            if ($authkey != '') {
                $tracking_number = $ninja->getTrackingNumber($authkey, $requesttracknumber, $deliveryname, $deliveryemail, $request->ship_mobile, $request->ship_ads1, $request->ship_ads2, $request->ship_zip, $request->pickup_instruction, $request->delivery_instructions, $request->weight, $request->size);
            }
        }

        $ship_tracking_number = $tracking_number;
        $size = $request->size;
        $weight = $request->weight;
        $ship_ads1 = $request->ship_ads1;
        $ship_ads2 = $request->ship_ads2;
        $ship_country = $request->ship_country;
        $ship_zip = $request->ship_zip;
        $ship_mobile = $request->ship_mobile;
        $pickup_instruction = $request->pickup_instruction;
        $delivery_instructions = $request->delivery_instructions;

        OrderDeliveryInfo::where('delivery_id', '=', $request->deliveryinfoid)->where('order_id', '=', $order_id)->update(array('ship_tracking_number' => $ship_tracking_number, 'shipping_by' => $shipping_by, 'size' => $size, 'weight' => $weight, 'ship_ads1' => $ship_ads1, 'ship_ads2' => $ship_ads2, 'ship_country' => $ship_country, 'ship_zip' => $ship_zip, 'ship_mobile' => $ship_mobile, 'pickup_instruction' => $pickup_instruction, 'delivery_instructions' => $delivery_instructions));

        return redirect('admin/orders/' . $order_id . '/multipledelivery')->with('success', 'Delivery Info Successfully Updated!');
    }

    public function multipledelivery($id)
    {
        $order = Order::where('order_id', '=', $id)->first();
        $orderdetails = OrderDetails::where('order_id', '=', $id)->get();
        $orderdeliveryinfo = OrderDeliveryInfo::where('order_id', '=', $id)->get();

//$orderdeliverydetails = OrderDeliveryDetails::where('order_id', '=', $id)->get();
        return view('admin/Order.multipledelivery', compact('order', 'orderdetails', 'orderdeliveryinfo'));
    }

    public function updatemultipledelivery(Request $request)
    {
        //print_r($request->all()); exit;
        $bulkaction = $request->bulk_action;
        if ($bulkaction == 'delivery') {
            $status = 1;
        } else {
            $status = 2;
        }

        $orderid = $request->orderid;
        $detailids = $request->prodids;

        $orderdeliveryinfo = new OrderDeliveryInfo;
        $orderdeliveryinfo->order_id = $request->orderid;
        $orderdeliveryinfo->order_status = $status;
        $orderdeliveryinfo->status = $status;
        $orderdeliveryinfo->shipment_appdt = date('Y-m-d H:i:s');
        $orderdeliveryinfo->delivered_date = date('Y-m-d');
        if ($request->next_delivery_date != '') {
            $orderdeliveryinfo->next_delivery_date = date('Y-m-d', strtotime($request->next_delivery_date));
        }
        $orderdeliveryinfo->save();

        $orderdeliveryinfoid = 0;
        $deliveryinfo = OrderDeliveryInfo::orderBy('delivery_id', 'desc')->select('delivery_id')->first();
        if ($deliveryinfo) {
            $orderdeliveryinfoid = $deliveryinfo->delivery_id;
        }

        //print_r($detailids);
        if (!empty($detailids) && is_array($detailids)) {
            foreach ($detailids as $detailid) {
                $field = 'detail_id_' . $detailid;
                $qty = $request->{$field};
                $product = OrderDetails::where('detail_id', '=', $detailid)->select('prod_id')->first();
                //print_r($product);
                if (isset($product)) {
                    $productid = $product->prod_id;
                    $orderdeliverydetails = new OrderDeliveryDetails;
                    $orderdeliverydetails->delivery_info_id = $orderdeliveryinfoid;
                    $orderdeliverydetails->order_id = $orderid;
                    $orderdeliverydetails->detail_id = $detailid;
                    $orderdeliverydetails->prod_id = $productid;
                    $orderdeliverydetails->quantity = $qty;
                    $orderdeliverydetails->status = $status;
                    //print_r($orderdeliverydetails);

                    $orderdeliverydetails->save();
                }
            }
        }
        //exit;
        return redirect('/admin/orders/' . $orderid . '/multipledelivery')->with('success', 'Order Delivery Status Successfully Updated!');
    }

    public function show($id)
    {
        $orders = OrderMaster::where('order_id', '=', $id)->first();
        $orderdetails = OrderDetails::where('order_id', '=', $id)->get();
        $orderdeliveryinfo = OrderDeliveryInfo::where('order_id', '=', $id)->orderBy('delivery_id', "desc")->first();

        $shipCountryData = Country::where('countrycode', $orders->ship_country)->first();
        $billCountryData = Country::where('countrycode', $orders->bill_country)->first();

        $settings = Settings::where('id', '=', '1')->first();
        return view('admin/Order.orderdetails', compact('orders', 'id', 'orderdetails', 'settings', 'orderdeliveryinfo', 'shipCountryData', 'billCountryData'));
    }

    public function destroy($id)
    {
        $order = OrderMaster::where('order_id', '=', $id)->first();
        $orderdetails = OrderDetails::where('order_id', '=', $id)->get();
        if ($order) {
            DB::insert('insert into order_master_backup select * from order_master where order_id = "' . $id . '"');
        }
        if ($orderdetails) {
            DB::insert('insert into order_details_backup select * from order_details where order_id = "' . $id . '"');
        }
        OrderMaster::where('order_id', '=', $id)->delete();
        OrderDetails::where('order_id', '=', $id)->delete();
        return redirect('/admin/orders')->with('success', 'Order Successfully Deleted!');
    }

    public function trackorder($orderid)
    {
        $chkorder = OrderDeliveryInfo::where('delivery_id', '=', $orderid)->select('ship_tracking_number')->first();
        if ($chkorder) {
            $trackingnumber = $chkorder->ship_tracking_number;
            $ninja = new Order;
            if (Session::has('ninja_authkey')) {
                $authkey = Session::get('ninja_authkey');
            } else {
                $authkey = $ninja->getAuthKey();
                Session::put('ninja_authkey', $authkey);
            }

            if ($authkey) {
                $ninja->TrackOrder($authkey, $trackingnumber);
            }
        }
    }

    public function removeOrderItems(Request $request, OrderServices $orderServices)
    {
        $order = Order::where('order_id',$request->order_id)->first();
        if(empty($order)) return response()->json(['status' => false,'message' => 'order is not found.']);
     
        $this->saveOrderHistory($request->order_id, $orderServices);
     
        $alphabets = range('A','Z');
        $totalAmount = 0;
        $orderNumber = $orderServices->manipulateOrderNumber($order->order_id,$order->date_entered);
        $newOrderAltLabel = $alphabets[$order->order_updated_times];
        $orderNumber .= $order->order_updated_times == 0 ? '' : $order->order_alt_text;
        
        $orderItems = OrderDetails::select('detail_id','order_id','Weight', 'prod_code', 'prod_id', 'prod_name', 'prod_option', 'prod_quantity', 'prod_unit_price')
            ->selectRaw("'' as order_no")
            ->where('order_id', '=', $request->order_id)->whereIn('detail_id',$request->items)->get();
        
        foreach($orderItems as $ork => $orderItem){
            $orderItems[$ork]['order_no'] = trim($orderNumber);
            $totalAmount+=($orderItem['prod_quantity']*$orderItem['prod_unit_price']);
        }
        $orderItems = $orderItems->toArray();
        //Insert into history
        //foreach($orderItems as $item) OrderUpdateHistory::create($item);

        //Remove old Items
        OrderDetails::where('order_id', '=', $request->order_id)->whereIn('detail_id',$request->items)->delete();

        //Update order lable
        Order::where('order_id',$request->order_id)->update([
            'order_updated_times' => $order->order_updated_times+1,
            'payable_amount' => ($order->payable_amount - $totalAmount),
            'order_alt_text' => $newOrderAltLabel
        ]);

        return response()->json(['status' => true, 'message' => 'Successfully item has been removed']);
    }
    
    public function calculateProdPrice(Request $request,$customData = NULL){
        
        return response()->json($request->all());
    }
    
    public function saveOrderHistory($orderId, $orderServices){
        $orderData = Order::where('order_id',$orderId)->first();
        $orderDataNo = $orderServices->manipulateOrderNumber($orderData->order_id,$orderData->date_entered).trim($orderData->order_alt_text);
        $data = [
            'order_id' => $orderId,
            'order_no' => $orderDataNo,
            'order_data' => serialize($orderData->toArray()),
            'order_items' => serialize(OrderDetails::where('order_id',$orderId)->get()->toArray()),
        ];
        OrderUpdateHistory::create($data);
    }
    
    public function updateOrderNo($orderId){
        $order = Order::where('order_id',$orderId)->first();
        $alphabets = range('A','Z');
        $newOrderAltLabel = $alphabets[$order->order_updated_times] ?? '';
        
        Order::where('order_id',$orderId)->update([
            'order_updated_times' => $order->order_updated_times+1,
            'order_alt_text' => $newOrderAltLabel
        ]);
    }
    
    public function updateOrderItem(Request $request, OrderServices $orderServices){
        
        $this->saveOrderHistory($request->order_id, $orderServices);
        
        if($request->action == 'update_item'){
            
            $data = [
                'detail_id' => $request->detail_id,
                'order_id' => $request->order_id,
                'prod_quantity' => $request->quantity,
                'prod_unit_price' => $request->price,
                'prod_quantity' => $request->quantity,
                'prod_option' => $request->variant_id == 0 ? '' : ProductOptions::where('Id',$request->variant_id)->first()->Title
            ];
            
            OrderDetails::where([
                ['detail_id','=',$request->detail_id],
                ['order_id','=',$request->order_id]
            ])->update($data);
            
            $this->updateOrderNo($request->order_id);
        }
        
        if($request->action == 'update_total'){
            
            $data = [
                'shipping_cost' => $request->shipping,
                'fuelcharges' => $request->fuel,
                'handlingfee' => $request->handling,
                'packaging_fee' => $request->packaging,
                'tax_collected' => $request->tax,
                'discount_amount' => $request->discount,
                'payable_amount' => $request->tot_price,
            ];
            
            Order::where('order_id',$request->order_id)->update($data);
            $this->updateOrderNo($request->order_id);

        }
        
        return response()->json([
            'status' => true,
            'message' => 'Successfully updated.'
        ]);
    }
    
    public function addMoreItemsToOrder(Request $request, OrderServices $orderServices){
        
        $this->saveOrderHistory($request->order_id, $orderServices);
        foreach($request->items as $item){
            
            $product = Product::where('Id',$item['product_id'])->first();
            $productOption = empty($item['variant_id']) ? NULL : ProductOptions::where([['Prod','=',$item['product_id']],['Id','=',$item['variant_id']]])->first();
            $weight = empty($item['variant_id']) ? $product->Weight : $productOption->Weight;
            
            $orderItems = [
                'order_id' => $request->order_id,
                'prod_id' => $item['product_id'],
                'prod_name' => $product->EnName,
                'prod_option' => empty($item['variant_id']) ? NULL : $productOption->Title,
                'prod_quantity' => $item['quantity'],
                'prod_unit_price' => $item['unit_price'],
                'Weight' => $weight
            ];
            OrderDetails::insert($orderItems);
        }
        
        $order = Order::where('order_id',$request->order_id)->first();
        $alphabets = range('A','Z');
        $newOrderAltLabel = $alphabets[$order->order_updated_times] ?? '';
        
        Order::where('order_id',$request->order_id)->update([
            'order_updated_times' => $order->order_updated_times+1,
            'order_alt_text' => $newOrderAltLabel,
            'payable_amount' => $order->payable_amount + $request->total_amount
        ]);
            
        return response()->json([
            'status' => true
        ]);
    }
    
    public function searchProd(Request $request){
        $allProducts = ($request->has('term') && $request->term!='') ? Product::selectRaw("id, EnName as text")->whereRaw("LOWER(EnName) LIKE '%".strtolower($request->term)."%'")->where('ProdStatus',1)->orderBy('EnName')->get() : [];
        return response()->json(['results' => $allProducts]);
    }

    public function fetchOtionsAndPrice(Request $request){
        
        $product = Product::find(trim($request->product_id));
        $variants = ProductOptions::where([['Prod','=',trim($request->product_id)],['Status','=',1]])->get();
        
        $itemPrice = 0;
        
        if(count($variants)==0){
            $itemPrice = $product->Price;
			$price = new \App\Models\Price();
			$actualprice = $price->getGroupPrice($product->Id);
			$itemPrice = $price->getDiscountPrice($product->Id);
			
			if($itemPrice<$actualprice) $itemPrice = $itemPrice;
        }
        
        return response()->json([
            'status' => true,
            'data' => [
                'variants' => $variants,
                'item_price' => $itemPrice
            ]
        ]);
    }
}
