<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cart extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model('Product_model');
		$this->load->model('Category_model');
		$this->load->model('Customer_model');
		//$this->load->model('Cart_model');
		//$this->load->model('Newsitem_model');
		//$this->load->model('Settings_model');
		//$this->load->model('Page_model');
		$this->load->library('cart');
	}
	public function index()
	{
		$data['category_list'] = $this->Category_model->GetCategories();
		$data['product_list'] = $this->Cart_model->GetCart();
		$data['news_list'] = $this->Newsitem_model->GetNewsitem();
		$this->load->model('Tags_model');
		$data['tag_list'] = $this->Tags_model->GetTags();
		$data['setting_list'] = $this->Settings_model->GetSettings();
		$data['page_list'] = $this->Page_model->GetPages();
		$this->load->view('myCart', $data);
	}
	public function MyCart()
	{
		$data['cat_display'] = "hide";
		$data['product_name_list'] = $this->Product_model->GetProductNames();
		$data['category_list'] = $this->Category_model->GetCategories();

		//$userEmail = $this->session->email;
		//$id = $this->Customer_model->getId($userEmail);

		//$data['category_list'] = $this->Category_model->GetCategories();
		//$data['cart_list'] = $this->Cart_model->GetCart();
		//$data['product_list']=$this->Product_model->GetProducts();
		//$this->load->model('Tags_model');
		//$data['tag_list'] = $this->Tags_model->GetTags();
		//$data['news_list'] = $this->Newsitem_model->GetNewsitem();
		//$data['setting_list'] = $this->Settings_model->GetSettings();
		//$data['page_list'] = $this->Page_model->GetPages();
		$this->load->view('shoppingcart', $data);
	}
	public function checkOut()
	{
		$this->load->model('Order_model');
		$this->load->library('form_validation');
		$this->form_validation->set_rules('fullName', 'Customer Name', 'required');
		$this->form_validation->set_rules('address_1', 'Address', 'required');
		$this->form_validation->set_rules('contact', 'Cell Number', 'required');
		if ($this->form_validation->run() == FALSE) {
			$data['setting_list'] = $this->Settings_model->GetSettings();
			$data['page_list'] = $this->Page_model->GetPages();
			$data['category_list'] = $this->Category_model->GetCategories();
			$data['product_list'] = $this->Product_model->GetProducts();
			$this->load->model('Tags_model');
			$data['tag_list'] = $this->Tags_model->GetTags();
			$data['news_list'] = $this->Newsitem_model->GetNewsitem();
			$this->load->view("checkout", $data);
		} else {
			$data['userId'] = $this->input->post('userid');
			$data['cartGrandTotal'] = $this->input->post('grandtotal');
			$data['address'] = $this->input->post('address_1');
			$data['contact'] = $this->input->post('contact');
			$data['orderDate'] = date('Y-m-d');
			$data['status'] = "Pending";
			$orderId = $this->Order_model->SaveOrder($data);


			foreach ($this->cart->contents() as $cart) {
				$orderDetail['orderid'] = $orderId;
				$orderDetail['productid'] = $cart['id'];
				$orderDetail['quantity'] = $cart['qty'];
				$orderDetail['price'] = $cart['price'];
				$orderDetail['discount'] = 0;
				$this->Order_model->SaveOrderDetail($orderDetail);
			}
			$to_email = $this->input->post('email');
			$this->emailSend($to_email);
			$this->cart->destroy();
			$data['category_list'] = $this->Category_model->GetCategories();
			$data['product_list'] = $this->Product_model->GetProducts();
			$data['news_list'] = $this->Newsitem_model->GetNewsitem();
			$data['page_list'] = $this->Page_model->GetPages();
			$this->load->view("mycart", $data);
		}
	}

	public function addCart($id)
	{

		//$this->load->library('cart');
		$dataExist = false;

		$qtyMsg = '';

		$cartData = $this->cart->contents();

		foreach ($cartData as $cart) {
			if ($cart['id'] == $id) {
				if ($cart['qty'] < $cart['cartData']['maxQty']) {
					$qty = $cart['qty'] + 1;

					$data = array(
						'rowid' => $cart['rowid'],
						'qty'   => $qty
					);

					$this->cart->update($data);
				} else {
					$qtyMsg = "Product maximum cart limit is " . $cart['cartData']['maxQty'];
				}

				$dataExist = true;
				break;
			}
		}

		if (!$dataExist) {
			$product = $this->Product_model->getProductById($id);
			$dataForm['ProductName'] = $product[0]['Name'];
			$dataForm['Price'] = $product[0]['Price'];
			$dataForm['minCartQty'] = $product[0]['MinCartQty'];

			$cartData = array(
				'image' => $product[0]['Picture'],
				'minQty' => $dataForm['minCartQty'],
				'maxQty' => $product[0]['MaxCartQty']
			);

			$data = array(
				'id'      => $id,
				'qty'     => $dataForm['minCartQty'],
				'price'   => $dataForm['Price'],
				'name'    => $dataForm['ProductName'],
				'cartData' => $cartData
			);

			$this->cart->insert($data);
		}

		$this->session->set_flashdata('cartAdded', 'You Product is Added to cart');

		$resp = array(
			"count" => count($this->cart->contents()),
			"msg" => $qtyMsg
		);
		echo json_encode($resp);
	}

	public function delete()
	{

		$data = array(
			'rowid' => $_POST['id'],
			'qty'   => 0
		);

		$this->cart->update($data);

		echo json_encode(count($this->cart->contents()));
	}

	public function updateQuantity()
	{
		$rowid = $_POST['id'];
		$qty = $_POST['quantity'];
		$maxQty=$this->cart->get_item($rowid)["cartData"]["maxQty"];
		$minQty=$this->cart->get_item($rowid)["cartData"]["minQty"];

		$qtyMsg='';

		if ($qty <= $maxQty && $qty >= $minQty) {
			$data = array(
				'rowid' => $rowid,
				'qty'   => $qty
			);
			$this->cart->update($data);
		}else{
			$qtyMsg="Product maximum cart limit is " . $maxQty." & minimum limit is ".$minQty;
		}

		$resp = array(
			"price" => $this->cart->get_item($rowid)['price'],
			"msg" => $qtyMsg,
			"qty"=>$this->cart->get_item($rowid)["qty"]
		);
		echo json_encode($resp);
	}

	public function showOrder()
	{
		$this->load->view('admin/manageorder');
	}

	public function emailSend($to_email)
	{
		$config = array(
			'protocol' => 'smtp',
			'smtp_host' => 'ssl://webs02.futuresouls.com',
			'smtp_port' => 465,
			'smtp_user' => 'muhammadtalha@eshopperpk.com',
			'smtp_pass' => 'talha112@uetlhr',
			'mailtype'  => 'html',
			'charset'   => 'iso-8859-1'
		);


		$this->load->library('email');
		$this->email->initialize($config);

		$from_email = "muhammadtalha@eshopperpk.com";

		//Load email library
		$this->load->library('email');
		$this->email->from($from_email, 'Identification');
		$this->email->to($to_email);
		$this->email->subject('Send Email Codeigniter');
		$this->email->message('The email send using codeigniter library');
		//Send mail
		if ($this->email->send())
			$this->session->set_flashdata("email_sent", "Congragulation Email Send Successfully.");
		else
			$this->session->set_flashdata("email_sent", "You have encountered an error");
	}
}
