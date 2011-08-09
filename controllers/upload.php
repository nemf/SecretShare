<?php

class Upload extends CI_Controller {

	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
	}

	function index()
	{
		$this->load->view('upload_form', array('error' => ' ' ));
	}

	function do_upload()
	{
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
		$this->benchmark->mark('upload_end');

			$data = array('upload_data' => $this->upload->data());

			// get algorithm, redundant, number of dispersal values
			$data['upload_data']['alg'] = $_REQUEST['a'];
			$data['upload_data']['rdd'] = $_REQUEST['k'];
			$data['upload_data']['num'] = $_REQUEST['n'];

			// encode (ss, ida, zfec)
			$cmd = "/usr/bin/cryptest.exe ";
			$cmd .= $data['upload_data']['alg'] . " ";
			$cmd .= $data['upload_data']['num'] - $data['upload_data']['rdd'] . " ";
			$cmd .= $data['upload_data']['num'] . " ";
			$cmd .= $data['upload_data']['full_path'] . "";

			$this->benchmark->mark('encode_start');
			$ret = system(preg_quote($cmd), $retval);
			if ( $retval )
			{
				$this->load->view('upload_form', $ret);
			}
			else 
			{

				$this->benchmark->mark('encode_end');

				// encode time 
				$data['upload_data']['encode_time'] = 
					$this->benchmark->elapsed_time('encode_start', 'encode_end') . " sec";

				// share size
				$list = glob($data['upload_data']['full_path'].".00*"); 
				$data['upload_data']['share_size'] = round(filesize($list[0])/1024,2);
				$data['upload_data']['share_size_total'] = round($data['upload_data']['share_size'] * $data['upload_data']['num'] ,2);


				// Throughput
				$data['upload_data']['throughput_encode'] = 
					round($data['upload_data']['file_size'] / $data['upload_data']['encode_time'], 2) . " Kbyte/sec";
					

				// decode
				switch ($data['upload_data']['alg'])
				{
					case "ss":
						$alg = "sr";
						break;

					case "id":
						$alg = "ir";
						break;
				}

				$reconst = "";

				for ($i=0; $i<($data['upload_data']['num'] - $data['upload_data']['rdd']); $i++)
				{
					$reconst .= $list[$i] . " ";
				}
				$cmd = "/usr/bin/cryptest.exe ";
				$cmd .= $alg . " ";
				$cmd .= $data['upload_data']['full_path'] . ".recover ";
				$cmd .= $reconst;
				
				$this->benchmark->mark('decode_start');
				$ret = system(preg_quote($cmd), $retval);
				if ( $retval )
				{
					$this->load->view('upload_form', $ret);
				}
				else
				{
					$this->benchmark->mark('decode_end');

					// decode time
					$data['upload_data']['decode_time'] =
						$this->benchmark->elapsed_time('decode_start', 'decode_end') .         " sec";

					// Throughput
					$data['upload_data']['throughput_decode'] = 
						round($data['upload_data']['file_size'] / $data['upload_data']['decode_time'], 2) . " Kbyte/sec";

					$this->load->view('upload_success', $data);
				}
			}

		}
	}
}
?>
