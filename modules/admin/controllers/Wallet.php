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
		$this->invoice();
	}

	public function invoice()
	{
		is_access_method($permission_type=1,$sec_id='23');
		$this->mem_top_menu_section = 'wallet';
		$data['heading_title'] = "Invoice and Payment";
		$data['current_balance'] = 0;// get_available_wallet_amount($this->userId,1);
		$this->load->view("wallet/view_wallet_payment",$data);
	}

	public function user_earning()
	{
		is_access_method($permission_type=1,$sec_id='24');
		$this->mem_top_menu_section = 'wallet';
		$data['heading_title'] = "User Earning Report";
		$label_cond = "wl.status='1'" . ($this->userId > 0 && $this->mres['member_type'] == '2' ? " AND (cus.parent_id = '{$this->userId}' OR wl.member_id = '{$this->userId}')" : '') 
            . ($this->userId > 0 && $this->mres['member_type'] == '3' ? " AND wl.member_id = '{$this->userId}'" : '');
        $param_label = [
            'where' => $label_cond,
            'exjoin' => [['tbl' => 'wl_customers as cus', 'condition' => "cus.customers_id=wl.member_id AND cus.status='1'", 'type' => 'LEFT']],
            'groupby' => 'wl.label_id',
            'debug' => FALSE
        ];
        $data['labels'] = $labels = $this->admin_model->get_labels($param_label);
// 		$data['labels'] = get_db_multiple_row('wl_labels','label_id,channel_name',"status='1'" . ($this->mres['member_type'] != 1 ? " AND member_id=" . $this->userId : ""));
		$data['current_balance'] = 0;// get_available_wallet_amount($this->userId,1);
		$this->load->view("wallet/view_user_earning",$data);
	}

	public function commission()
	{
		is_access_method($permission_type=1,$sec_id='25');
		$this->mem_top_menu_section = 'wallet';
		$data['heading_title'] = "Commission Amount";
		$data['current_balance'] = 0;// get_available_wallet_amount($this->userId,1);
		$this->load->view("wallet/view_wallet_commission",$data);
	}

	public function revenue_graph()
	{
		is_access_method($permission_type=1,$sec_id='26');
		$this->mem_top_menu_section = 'wallet';
		$data['heading_title'] = "Revenue Graph";
		$label_cond = "wl.status='1'" . ($this->userId > 0 && $this->mres['member_type'] == '2' ? " AND (cus.parent_id = '{$this->userId}' OR wl.member_id = '{$this->userId}')" : '') 
            . ($this->userId > 0 && $this->mres['member_type'] == '3' ? " AND wl.member_id = '{$this->userId}'" : '');
        $param_label = [
            'where' => $label_cond,
            'exjoin' => [['tbl' => 'wl_customers as cus', 'condition' => "cus.customers_id=wl.member_id AND cus.status='1'", 'type' => 'LEFT']],
            'groupby' => 'wl.label_id',
            'debug' => FALSE
        ];
        $data['labels'] = $labels = $this->admin_model->get_labels($param_label);
		$data['current_balance'] = 0;// get_available_wallet_amount($this->userId,1);
		$this->load->view("wallet/view_revenue_graph",$data);
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
}
/* End of file wallet.php */
/* Location: .application/modules/documents/wallet.php */