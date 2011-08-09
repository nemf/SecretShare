<?php

$data = "";

class Upload2 extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}

	function index()
	{
		$this->load->view('upload_form2', array('error' => ' ' ));
	}

	function do_upload()
	{
		global $data;

		$config['upload_path'] = './uploads/';
		$config['allowed_types'] = '*';
		$config['max_size']	= '2048'; // KB

		$this->load->library('upload', $config);
		$this->output->enable_profiler(TRUE);

		$this->benchmark->mark('upload_start');

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			$this->load->view('upload_form', $error);
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());
			$this->benchmark->mark('upload_end');
		
			$this->benchmark->mark('encode_start');
			$this->_encode();
			$this->benchmark->mark('encode_end');

			$this->benchmark->mark('decode_start');
			$this->_decode();
			$this->benchmark->mark('decode_end');

			$this->_statistics();

echo "<pre>";
//print_r($data);
echo "</pre>";

			$this->load->view('upload_success', $data);

		}
	}

	function _encode()
	{
		global $data;

		// get algorithm, redundant, number of dispersal values
	    $data['upload_data']['alg'] = $_REQUEST['a'];
	    $data['upload_data']['rdd'] = $_REQUEST['k'];
	    $data['upload_data']['num'] = $_REQUEST['n'];

		// encode (ss, ida, zfec)
		switch ($data['upload_data']['alg'])
		{
		    case "ss":
		    case "id":
				$cmd = "/usr/bin/cryptest.exe ";
				$cmd .= $data['upload_data']['alg'] . " ";
				$cmd .= $data['upload_data']['num'] - $data['upload_data']['rdd'] . " ";
				$cmd .= $data['upload_data']['num'] . " ";
				$cmd .= $data['upload_data']['full_path'] . "";
		        break;

		    case "zfec":
				$cmd = "/usr/bin/zfec -q -k ";
				$cmd .= $data['upload_data']['num'] - $data['upload_data']['rdd'];
				$cmd .= " -m " . $data['upload_data']['num'] . " ";
				$cmd .= $data['upload_data']['full_path'] . "";
		        break;
		}

		$ret = system(preg_quote($cmd), $retval);
		
		return $retval;
	}

	function _decode()
	{
		global $data;
		$reconst = "";

		// decode
		switch ($data['upload_data']['alg'])
		{
		    case "ss":
		    case "id":
				if ( $data['upload_data']['alg'] == "ss" )
				{
		        	$alg = "sr";
				}
				else
				{
		        	$alg = "ir";
				}

        		$data['upload_data']['share_list'] = glob($data['upload_data']['full_path'].".00*");
				for ($i=0; $i<($data['upload_data']['num'] - $data['upload_data']['rdd']); $i++)
				{
				    $reconst .= $data['upload_data']['share_list'][$i] . " ";
				}
				$cmd = "/usr/bin/cryptest.exe ";
				$cmd .= $alg . " ";
				$cmd .= $data['upload_data']['full_path'] . ".recover ";
				$cmd .= $reconst;
		        break;

		    case "zfec":
		    	$data['upload_data']['share_list'] = glob($data['upload_data']['full_path']."*.fec");
				for ($i=0; $i<($data['upload_data']['num'] - $data['upload_data']['rdd']); $i++)
				{
				    $reconst .= $data['upload_data']['share_list'][$i] . " ";
				}
				$cmd = "/usr/bin/zunfec -f -o ";
				$cmd .= $data['upload_data']['full_path'] . ".recover ";
				$cmd .= $reconst;
		       	break;
		}

		$ret = system(preg_quote($cmd), $retval);
		
		return $retval;
	}

	function _statistics()
	{
		global $data;

		// encode/decode time
		$data['upload_data']['encode_time'] =
		    $this->benchmark->elapsed_time('encode_start', 'encode_end') . " sec";

		$data['upload_data']['decode_time'] =
		    $this->benchmark->elapsed_time('decode_start', 'decode_end') . " sec";

		// share size (kbyte)
        $data['upload_data']['share_size'] = round(filesize($data['upload_data']['share_list'][0])/1024,2);
        $data['upload_data']['share_size_total'] = 
				round($data['upload_data']['share_size'] * $data['upload_data']['num'] ,2);

         // encode/decode throughput
        $data['upload_data']['throughput_encode'] =
				round($data['upload_data']['file_size'] / $data['upload_data']['encode_time'], 2) . " Kbyte/sec";
        $data['upload_data']['throughput_decode'] =
				round($data['upload_data']['file_size'] / $data['upload_data']['decode_time'], 2) . " Kbyte/sec";
	}
}
?>
