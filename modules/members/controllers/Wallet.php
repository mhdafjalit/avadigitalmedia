<?php
class Wallet extends Private_Controller
{

	private $mId;

	public function __construct()
	{
		parent::__construct();
		$this->load->model(array('admin/admin_model','order/order_model'));
		$this->load->helper(array('wallets'));
	    $this->load->library(array('safe_encrypt', 'Dmailer'));
	    $this->form_validation->set_error_delimiters("<div class='required'>","</div>");
	}

	public function index()
	{
		$this->payment_request();
	}

	public function payment_request()
	{
		$this->mem_top_menu_section = 'wallet';
		$data['heading_title'] = "Payment Request";
		$data['current_balance'] = 0;// get_available_wallet_amount($this->userId,1);
		$this->load->view("wallet/view_wallet_payment_request",$data);
	}

	public function invoice()
	{
		$this->mem_top_menu_section = 'wallet';
		$data['heading_title'] = "Invoice and Payment";
		$data['current_balance'] = 0;// get_available_wallet_amount($this->userId,1);
		$this->load->view("wallet/view_wallet_payment",$data);
	}

	public function user_earning()
	{
		$this->mem_top_menu_section = 'wallet';
		$data['heading_title'] = "User Earning Report";
		$data['current_balance'] = 0;// get_available_wallet_amount($this->userId,1);
	    $label_cond = "wl.status='1'" . ($this->userId > 0 && $this->mres['member_type'] == '2' ? " AND (cus.parent_id = '{$this->userId}' OR wl.member_id = '{$this->userId}')" : '') 
            . ($this->userId > 0 && $this->mres['member_type'] == '3' ? " AND wl.member_id = '{$this->userId}'" : '');
        $param_label = [
            'where' => $label_cond,
            'exjoin' => [['tbl' => 'wl_customers as cus', 'condition' => "cus.customers_id=wl.member_id AND cus.status='1'", 'type' => 'LEFT']],
            'groupby' => 'wl.label_id',
            'debug' => FALSE
        ];
        $data['labels'] = $labels = $this->admin_model->get_labels($param_label);
        //$data['labels'] = get_db_multiple_row('wl_labels','label_id,channel_name',"status='1'" . ($this->mres['member_type'] != 1 ? " AND member_id=" . $this->userId : ""));
		$this->load->view("wallet/view_user_earning",$data);
	}

	public function commission()
	{
		$this->mem_top_menu_section = 'wallet';
		$data['heading_title'] = "Commission Amount";
		$data['current_balance'] = 0;// get_available_wallet_amount($this->userId,1);
		$this->load->view("wallet/view_wallet_commission",$data);
	}

	public function revenue_graph()
	{
		$this->mem_top_menu_section = 'wallet';
		$data['heading_title'] = "Revenue Graph";
		$data['current_balance'] = 0;// get_available_wallet_amount($this->userId,1);
        $label_cond = "wl.status='1'" . ($this->userId > 0 && $this->mres['member_type'] == '2' ? " AND (cus.parent_id = '{$this->userId}' OR wl.member_id = '{$this->userId}')" : '') 
            . ($this->userId > 0 && $this->mres['member_type'] == '3' ? " AND wl.member_id = '{$this->userId}'" : '');
        $param_label = [
            'where' => $label_cond,
            'exjoin' => [['tbl' => 'wl_customers as cus', 'condition' => "cus.customers_id=wl.member_id AND cus.status='1'", 'type' => 'LEFT']],
            'groupby' => 'wl.label_id',
            'debug' => FALSE
        ];
        $data['labels'] = $labels = $this->admin_model->get_labels($param_label);
		//$data['labels'] = get_db_multiple_row('wl_labels','label_id,channel_name',"status='1'" . ($this->mres['member_type'] != 1 ? " AND member_id=" . $this->userId : ""));
		$this->load->view("wallet/view_revenue_graph",$data);
	}

	public function public_wallet()
	{
		$this->mem_top_menu_section = 'public_wallets';
		$data['heading_title'] = "Public Wallet";
		$per_page 		 = $this->config->item('per_page');
	  	$base_link       = site_url($this->uri->uri_string);
		$offset          = (int) $this->input->get_post('offset');
		$offset          = $offset<=0 ? 1 : $offset;
		$db_offset       = ($offset-1)*$per_page;
		$base_url        =   "documents/wallet/public_wallet";
		$from_date  = $this->db->escape_str(trim($this->input->get_post('from_date',TRUE)));
		$to_date    = $this->db->escape_str(trim($this->input->get_post('to_date',TRUE)));
		$whereWallet= "mwlt.wallet_type = '2' ";
		if($from_date!='' ||  $to_date!='')
		{
			$condition_date = array();
			$whereWallet .=" AND (";
			if($from_date!='')
			{
				$condition_date[] = "DATE(receive_date)>='$from_date'";
			}
			if($to_date!='')
			{
				$condition_date[] ="DATE(receive_date)<='$to_date'";
			}
			$whereWallet.=implode(" AND ",$condition_date)." )";
		}
		$sort_by_rec ="mwlt.id DESC";
		$paramWallets = array(
						'offset'=>$db_offset,
						'limit'=>$per_page,
						'where'=>$whereWallet,
						'orderby'=>$sort_by_rec,
						'groupby'=>'mwlt.id',
						'debug'=>FALSE
						);
		$res_array              = $this->members_model->get_members_wallet($paramWallets);
		$data['total_records'] 	= $total_activity = $this->members_model->total_rec_found;
		$data['frm_url'] 		= $base_url;
		$params_pagination = array(
		'base_link'=>$base_link,
		'base_link'=>$base_link,
		'data_form'=>'#transactions_frm',
		'per_page'=>$per_page,
		'total_recs'=>$total_activity,
		'uri_segment'=>$offset,
		'refresh'=>1
		);
		$page_links     = front_pagination($params_pagination);
		$data['page_links'] = $page_links;
		$data['base_link'] = $base_link;
		$data['offset'] = $offset;
		$data['res'] 	= $res_array;
		$data['current_balance'] = get_public_wallet_amount(2);
		$this->load->view("wallet/view_public_wallet",$data);
	}

	public function fund_transfer()
	{
		$wallet_type = (int) $this->uri->segment(4);
		$this->mem_top_menu_section = 'my_wallet';
		$data['heading_title'] = "Add Request for the Fund Transfer";
		if($this->input->post('action')!='')
		{
			$current_balance = ($wallet_type=='1')? get_available_wallet_amount($this->userId,1): get_public_wallet_amount(2);
			$this->form_validation->set_rules('receiver_type',"Organization",'trim|required');
			$this->form_validation->set_rules('member_id',"Member Name",'trim|required');
			$this->form_validation->set_rules('transaction_amount',"Amount",'trim|required|numeric|greater_than[0]|less_than_equal_to['.$current_balance.']');

			if($this->form_validation->run()===TRUE)
			{
				$sender_data = array(
				'user_id' 		=> $this->userId,
				'wallet_type' 	=> $wallet_type,
				'member_type' 	=> $this->mres['member_type'],
				'receiver_type' => $this->input->post('receiver_type'),
				'receiver_id' 	=> $this->input->post('member_id'),
				'matter_type' 	=> 'Amount transferred',
				'transaction_type' 	=> 'Dr',
				'transaction_amount' => $this->input->post('transaction_amount'),
				'receive_date' => $this->config->item('config.date.time'),
				'payment_status' => '0',
				'is_request' => '1'
				);

				$sender_data = $this->security->xss_clean($sender_data);
				$this->members_model->safe_insert('wl_wallet',$sender_data,FALSE);

				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success','Your fund transfer has been saved successfully.');
				redirect(current_url_query_string(), '');
			}
		}
		$this->load->view("wallet/view_wallet_fund_transfer",$data);
	}

	public function fund_request()
	{
		$this->mem_top_menu_section = 'my_wallet';
		$data['heading_title'] = "Request for the Funds";
		if($this->input->post('action')!=''){
			$this->form_validation->set_rules('wallet_type',"Wallet",'trim|required');
			$this->form_validation->set_rules('transaction_amount',"Amount",'trim|required|numeric|greater_than[0]');
			if($this->form_validation->run()===TRUE)
			{
				$posted_data = array(
				'user_id' 		=> $this->userId,
				'wallet_type' 	=> $this->input->post('wallet_type'),
				'member_type' 	=> $this->mres['member_type'],
				'matter_type' 	=> 'Amount added to Wallet',
				'transaction_amount' => $this->input->post('transaction_amount'),
				'receive_date' => $this->config->item('config.date.time'),
				'payment_status' => '0',
				'is_request' => '0'
				);
				$posted_data = $this->security->xss_clean($posted_data);
				$this->members_model->safe_insert('wl_wallet',$posted_data,FALSE);
				$this->session->set_userdata(array('msg_type'=>'success'));
				$this->session->set_flashdata('success','Your fund request has been saved successfully.');
				redirect(current_url_query_string(), '');
			}
		}
		$this->load->view("wallet/view_wallet_fund_request",$data);
	}

	public function organzation_members()
	{
		$member_type = (int) $this->input->post('receiver_type');
		$selected_id = (int) $this->input->post('current_selected');
		$condition ="m.status = '1' AND m.member_type='".$member_type."' ";
		$sort_by_rec ="m.customers_id DESC";
		$param_mem = array(
						'where'=>$condition,
						'orderby'=>$sort_by_rec,
						'groupby'=>'m.customers_id',
						'debug'=>FALSE
						);
		$res_array = $this->members_model->get_members($param_mem);
		$data['res'] =  $res_array;
		$data['selected_id']    = $selected_id;
		$data['option_val_field'] = 'customers_id';
		$data['option_text_field'] = 'first_name';
		$this->load->view('remote/load_attributes',$data);
	}
}
/* End of file wallet.php */
/* Location: .application/modules/documents/wallet.php */