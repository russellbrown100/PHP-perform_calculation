<?php



	function split_string($string, $separator)
	{

		$text = "";
		$open = "true";
		
		$list = array();
		
		for ($i = 0; $i < strlen($string); $i++)
		{
			
			if ($string[$i] == $separator)
			{
				if ($open == "true")
				{
					array_push($list, $text);
					$text = "";
				}
			}
			else
			{
				if ($string[$i] == '"')
				{
					if ($open == "true") $open = "false";
					else if ($open == "false") $open = "true";
					
				}
				
				$text = $text.$string[$i];
			}
		}
		
		array_push($list, $text);

		
		return $list;
		
	}
	
	

	function get_root()
	{
		return 'C:\\Users\\Russell Brown\\Documents\\PHP Scripts\\';
	}


	function get_data($string, $i)
	{
		$result = "";
		
		$left = $i - 1; $right = $i + 1; $c = 0;
		
		while (true)
		{
			if ($string[$left] == "\"")
			{
				$c++;
				if ($c == 2)
				{
					break;
				}
			}
			
			$left--;
			if ($left < 0) break;
		}
		
		
		
		while (true)
		{
			if ($string[$right] == "}" || $string[$right] == "," || $string[$right] == "\n")
			{
				break;
			}
			$right++;
			if ($right >= strlen($string)) break;
		}
		
		
		
		$data = substr($string, $left, $right - $left);
		
		
		
		
		
		$data = str_replace("\"", "", $data);
		
		
		$append = "true";
		
		
		for ($i2 = 0; $i2 < strlen($data); $i2++)
		{
			
			if ($append == "true")
			{
				if ($data[$i2] == ":")
				{				
					$append = "false";
					$result = $result.":";
				}
			}
			else if ($append == "false")
			{
				if ($data[$i2] != " ")
				{
					$append = "true";
				}
			}
			
			if ($append == "true")
			{
				$result = $result.$data[$i2];
			}
		}
		
		
		
		return $result;
	}

	function get_name($string)
	{
		$result = "";
		
		for ($i = 0; $i < strlen($string); $i++)
		{
			if ($string[$i] == ":")
			{
				$result = substr($string, 0, $i);
				
				break;
			}
		}
		
		return $result;
		
	}

	function get_value($string)
	{
		$result = "";
		
		for ($i = 0; $i < strlen($string); $i++)
		{
			if ($string[$i] == ":")
			{
			
				if ($i < strlen($string) - 1)
				{		
					$result = substr($string, $i + 1, strlen($string) - $i - 1);
					
				}
				
				break;
			
			}
			
			
		}
		
		return $result;
	}

	
		
	function trim_left($string, $char)
	{

		$text = "";
		
		for ($i = 0; $i < strlen($string); $i++)
		{			
			if ($string[$i] != $char)
			{
				$text = $text.$string[$i];
			}
		}
		
		return $text;
		
	}
	
	
	function trim_right($string, $char)
	{

		$text = "";
		
		for ($i = strlen($string) - 1; $i >= 0; $i--)
		{			
			if ($string[$i] != $char)
			{
				$text = $text.$string[$i];
			}
		}
		
		$text2 = "";
		
		for ($i = 0; $i < strlen($text); $i++)
		{			
			if ($string[$i] != $char)
			{
				$text2 = $text2.$string[$i];
			}
		}
		
		return $text2;
		
	}
		

	function sortDates($array)
	{
		$temp = null;
		for ($i = 1; $i < sizeof($array); $i++)
		{
			for ($j = $i; $j > 0; $j--)
			{
				$dt = DateTime::createFromFormat('Y-m-d', $array[$j]);
				$dt2 = DateTime::createFromFormat('Y-m-d', $array[$j - 1]);
				
				if ($dt < $dt2)
				{
					$tmp = $array[$j];
					$array[$j] = $array[$j - 1];
					$array[$j - 1] = $tmp;
				}
			}
		}
		
		return $array;
	}

	
	function get_beginning_of_line($f)
	{
		$position = ftell($f);
		
		if ($position > 0)
		{
			while (true)
			{
				fseek($f, $position, SEEK_SET);
				
				$c = fgetc($f);
				
				if ($c == "\n") break;
				
				$position--;
				
				if ($position <= 0) break;
			}
			
			if ($position > 0)
			{
				fseek($f, $position + 1, SEEK_SET);
			}
			else
			{
				fseek($f, $position, SEEK_SET);
			}
		}
	}



	//-------------------------------------------------------------------------------------------------------------------------------------------------------------

	function download_symbols()
	{
				
		$url = 'https://eodhistoricaldata.com/api/exchanges/US?api_token=5c79dea362d993.04812600&fmt=json';

		$data = file_get_contents($url);


		$path = get_root().'symbols.dat';
		
		if (file_exists($path))
		{
			unlink($path);
		}
		

		$file = fopen($path, "w+");





		$output_string = "";
		$count = 0;



		for ($i = 0; $i < strlen($data); $i++)
		{
			if ($data[$i] == ":")
			{
				
				$d = get_data($data, $i);
				$n = get_name($d);
				$v = get_value($d);
				


				if ($n == "Code" && strpos($v, "-") == false)
				{
					$output_string = $output_string.$v;
					$count++;
				}
				else if ($n == "Exchange")
				{			
					$output_string = $output_string."|".$v;
					$count++;
				}
				else if ($n == "Name" && 
				
					 strpos($v, "ETF") == false &&
					 strpos($v, "Fund") == false &&
				
					(strpos($v, "Inc") == true || 
					strpos($v, "Ltd") == true || 
					strpos($v, "Limited") == true || 
					strpos($v, "Corp") == true || 
					strpos($v, "Corporation") == true || 
					strpos($v, "Bank") == true ||
					strpos($v, "Group") == true)
					
					)
				{
					$output_string = $output_string."|".$v;
					$count++;
				}
				else if ($n == "Type" && $v != "null" && $v != "")
				{
					$output_string = $output_string."|".$v;
					$count++;
				}			


						
			}
			else if ($data[$i] == "}")
			{
				if ($count == 4)
				{
					$output_string = $output_string."\n";
					fwrite($file, $output_string);
				}
				
				$output_string = "";
				$count = 0;
			}
			
		}

		fclose($file);
		
		

		

		
	}
		
		

		
	function download_price_data()
	{	
		

		$output_path = get_root().'price_data.dat';		
				
		if (file_exists($output_path))
		{
			unlink($output_path);
		}
	
		$symbol_count = 0;
			
		
		$to_date = "2019-01-02";
		$from_date = "2019-02-02";
	
		$symbol_path = get_root().'symbols.dat';
		$symbol_file = fopen($symbol_path, "r");
		while (true)
		{
			$line = fgets($symbol_file);
			if ($line == null) break;
			$array = split_string($line, '|');
		
			$symbol = $array[0];
						
			print "downloading:  ".$symbol."   ".date("h:i")."\n";
			$output_file = fopen($output_path, "a+");	

			$url = "https://eodhistoricaldata.com/api/table.csv?s=".$symbol.".US&api_token=5c79dea362d993.04812600&a=1&b=1&c=2016&d=1&e=2&f=2019&g=d";	
			$data = file_get_contents($url);	
			
			$header = "true";
			
			$line = "";
			
			for ($i = 0; $i < strlen($data); $i++)
			{
				
				if ($data[$i] == "\n")
				{
					if ($header == "false")
					{
						$line = $array[0].",".$array[1].",".$line."\n";
						fwrite($output_file, $line);
					}
					$header = "false";
					$line = "";
				}
				else if ($data[$i] != "\r")
				{
					$line = $line.$data[$i];
				}
			}
						


			fclose($output_file);
			
			//if ($symbol_count >= 15) break;
			$symbol_count++;
			
		}	
		
		
		
		
	}
	
	
	
	function map_data()
	{
		
		
			
			
		print "compiling dates:\n";
		print date("h:i")."\n";

		$path = "C:\Users\Russell Brown\Documents\PHP Scripts\price_data - Copy (2).dat";
		$file = fopen($path,"r");		
		$line = "";
		$dates_list = array();	
		$last_line_number = 0;
		$c = 0;
		$region_list = array();
		$ending_line_number = 100000;
		$position = 0;
		$saved_region_position = 0;
		$saved_position = 0;
		$line_count = 0;

		while (true)
		{
			$data = fread($file, 1000000);		
			//print(ftell($file)."   ".strlen($data)."   ".filesize($path)."\n");		
			for ($i = 0; $i < strlen($data); $i++)
			{
				if ($data[$i] == "\n")
				{			
					$arr = split_string($line,',');						
					if (in_array($arr[2], $dates_list) == false)
					{
						array_push($dates_list, $arr[2]);
					}				
					
					$line = "";
					$line_count++;
					if ($line_count >= $ending_line_number)
					{
						$arr2 = array($saved_region_position, $position);				
						array_push($region_list, $arr2);
						$saved_region_position = $position;
						$ending_line_number += 100000;
						
						//if (sizeof($region_list) > 2) break;
							
						
					}
					
					
				}
				else if ($data[$i] != "\r")
				{
					$line = $line.$data[$i];
				}	
		
				$position++;					
			}
			$c++;
			if ($c >= 10)
			{
				//break;
			}			
						
			//if (sizeof($region_list) > 2) break;
			if (feof($file) == true) break;		
		}

		fclose($file);
		
			
		
		
		
		$last_line_number = $line_count;
		
		
		print "sorting dates:\n";
		
		$dates_list = sortDates($dates_list);
		
		//------------------------------------------------------
		
		// ensure old database is deleted
		
		print "mapping dates:\n";
		print date("h:i")."\n";
		
		$path2 = "C:\Users\Russell Brown\Documents\PHP Scripts\mapped_data.dat";
		
		if (file_exists($path2))
		{		
			unlink($path2);
		}	
		
		$saved_map_position = 0;
		
		//-----------
		
		$region_id = 0;
		
		$total_size = 0;
		
		while (true)
		{
				
			
			
		//	print "mapping date-region:  ".$region_id." of ".sizeof($region_list)."\n";

			$file = fopen($path,"r");		
			$line = "";
			$positions_list = array();	
			for ($i = 0; $i < sizeof($dates_list); $i++)
			{
				array_push($positions_list, array());
			}
					
			
			$region_arr = $region_list[$region_id];
			fseek($file, $region_arr[0], SEEK_SET);
			$saved_position = ftell($file);
			$position = $saved_position;
			$region_length = $region_arr[1] - $region_arr[0];
			$data = fread($file, $region_length);		
			//print(ftell($file)."   ".strlen($data)."   ".filesize($path)."\n");		
			for ($i = 0; $i < strlen($data); $i++)
			{
				if ($data[$i] == "\n")
				{			
					$arr = split_string($line,',');					
					$result = array_search($arr[2], $dates_list);
					if ($result == true)
					{
						$arr2 = $positions_list[$result];
						array_push($arr2, $saved_position);
						$positions_list[$result] = $arr2;
					}
					$saved_position = $position;
					$line = "";
					$line_count++;
				}
				else if ($data[$i] != "\r")
				{
					$line = $line.$data[$i];
				}			
				$position++;
			}	
			if (feof($file) == true) break;	
			if ($region_id == sizeof($region_list) - 1) break;
			fclose($file);
			
			
			
			
			
			//print "saving map:\n";
			//print date("h:i")."\n";
			
			
			$file2 = fopen($path2,"a+");	
			
			for ($i = 0; $i < sizeof($dates_list); $i++)
			{
				$saved_map_position = $total_size;
				$line = "positions for date: ".$dates_list[$i]."\n";
				fwrite($file2, $line);
				$total_size += strlen($line);
				
				$array = $positions_list[$i];		
				for ($i2 = 0; $i2 < sizeof($array); $i2++)
				{
					$line = $array[$i2]."\n";
					fwrite($file2, $line);
					$total_size += strlen($line);
				}
				$line = "saved map position:".$saved_map_position."\n";
				fwrite($file2, $line);
				$total_size += strlen($line);
				
				
				
				
			}
			
			fclose($file2);
			
			
			
			if ($region_id == sizeof($region_list) - 1) break;
			
			$region_id++;
		
		}	
		
		
		
		$dates_list = array();
		
		print "compiling root map:\n";
		print date("h:i")."\n";

		
		$path = "C:\Users\Russell Brown\Documents\PHP Scripts\mapped_data.dat";
		$file = fopen($path,"r+");
		
		
		
		fseek($file, 0, SEEK_END);
		$position = ftell($file);
		
		$roots = array();
		$root_positions = array();
		
		while (true)
		{
			
			//print(ftell($file)."   ".filesize($path)."\n");
			
			fseek($file, $position - 2, SEEK_SET);
			get_beginning_of_line($file);	
			$line = fgets($file);
			//print $line;
			$array = split_string($line, ':');	
			$position = $array[1];		
			
			if ($position == 0) break;
			
			fseek($file, $position, SEEK_SET);
			$line = fgets($file);
			//print $line;
			
			$array = split_string($line, ':');	
			$date = $array[1];
			$date = trim_left($date, ' ');
			$date = trim_right($date, "\n");
			
			if (in_array($date, $dates_list) == false)
			{
				array_push($dates_list, $date);
			}
			
			array_push($roots, $date);
			array_push($root_positions, $position);
			
			
		}
		
		
		print "saving root map:\n";
		print date("h:i")."\n";
		
		fseek($file, 0, SEEK_END);
		
		$date_positions = array();
		
		for ($di = 0; $di < sizeof($dates_list); $di++)
		{
		
			$target_date = $dates_list[$di];
			
			$date_pos = ftell($file);
			array_push($date_positions, $date_pos);
			
			$line = "date: ".$target_date."\n";
			
			fwrite($file, $line);
			
			for ($i = 0; $i < sizeof($roots); $i++)
			{
				if ($roots[$i] == $target_date)
				{
					$line = $root_positions[$i];
					fwrite($file, $line);
			//		print $root_positions[$i];
				}
			}
		
		
		}
		
		$pos = ftell($file);
		
		fwrite($file, "date positions:\n");
		
		for ($di = 0; $di < sizeof($dates_list); $di++)
		{
		
			$target_date = $dates_list[$di];			
			
			$line = $target_date.":".$date_positions[$di]."\n";
			fwrite($file, $line);
			
		}
		
		
		//fwrite($file, "beginning of date positions:".$pos);
		
		
		fclose($file);
		
		
		
		
		print "done\n";
		print date("h:i")."\n";

		
		
		
		
		
	}
	
	
	function normalize_data()
	{
		
		
		
		$output_path = 'C:\Users\Russell Brown\Documents\PHP Scripts\normalized_data.dat';
			
		if (file_exists($output_path))
		{		
			unlink($output_path);
		}	
		
		
		$output_file = fopen($output_path, "a+");
		
		$data_path = "C:\Users\Russell Brown\Documents\PHP Scripts\price_data - Copy (2).dat";
		$data_file = fopen($data_path, "r");
			
		$path = "C:\Users\Russell Brown\Documents\PHP Scripts\mapped_data.dat";
		$file = fopen($path,"r+");
		fseek($file, 0, SEEK_END);
		$position = ftell($file);
		
		$saved_size = 0;
		
		$i = 0;
		
		while (true)
		{	
			fseek($file, $position - 2, SEEK_SET);
			get_beginning_of_line($file);	
			$position = ftell($file);
			$line = fgets($file);
					
			if ($line == "date positions:\n") break;
		
		
			if ($i > 0)
			{
				
				$array = split_string($line, ':');
				$root_date = $array[0];
				$root_position = $array[1];
				
				print "date: ".$root_date."   ".date("h:i")."\n";
				
				fwrite($output_file, "--------------------------------------------------------------------------------------\n");
				fwrite($output_file, "data for date:  ".$root_date."\n");
				
				$saved_size = ftell($output_file);
				
				fseek($file, $root_position, SEEK_SET);
				$root_line = fgets($file);
				
				
				$map_list = array();
				
				$c = 0;
				while (true)
				{
					$root_line = fgets($file);
					
					if ($root_line == "date positions:\n") break;
					
					$array = split_string($root_line, ':');
					if ($array[0] == "date")
					{
						break;
					}
					else
					{
						
						$map_position = $root_line;
						
						$saved_position = ftell($file);
						fseek($file, $map_position, SEEK_SET);
						$map_line = fgets($file);						
						
						while (true)
						{
							$data_line = fgets($file);
							$array = split_string($data_line, ':');
							if ($array[0] == "saved map position")
							{
								break;
							}
							else
							{
								fseek($data_file, $data_line+1);
								$data_line = fgets($data_file);
								
								array_push($map_list, $data_line);
							}
						}
						
						
						fseek($file, $saved_position, SEEK_SET);
						
					}
					
					//$c++;
					//if ($c > 0)
					//{
					//	break;
					//}
				}
				
				
				$symbols_list = array();
				
				for ($i2 = 0; $i2 < sizeof($map_list); $i2++)
				{
                    $data_line2 = $map_list[$i2];
					$arr2 = split_string($data_line2, ',');
					array_push($symbols_list, $arr2[0]);
				}
				
				
				$sorted_symbol_list = $symbols_list;
				
				sort($sorted_symbol_list);
				
				$symbol_key_list = array();
				
				
				for ($i2 = 0; $i2 < sizeof($sorted_symbol_list); $i2++)
				{
					$id = array_search($sorted_symbol_list[$i2], $symbols_list);
					array_push($symbol_key_list, $id);
					
				}
				
				
				for ($i2 = 0; $i2 < sizeof($sorted_symbol_list); $i2++)
				{
					$id = $symbol_key_list[$i2];
					
					
					$ln = $map_list[$id];
					fwrite($output_file, $ln);
					
				}
				
			}
			
			$i++;
		//	if ($i > 5) break;
		
		}
		
			
			
		fclose($output_file);
		fclose($data_file);
		fclose($file);
			
	}

	
	
	
	function calculate_top_gains()
	{
		
		$path = "C:\Users\Russell Brown\Documents\PHP Scripts\symbols.dat";		
		$file = fopen($path,"r");		
		
		$symbol_list = array();
		$symbol_data = array();
		$chart_list = array();
		$chart_length = 30;
	
		
		
		while (true)
		{
			$line = fgets($file);
			if ($line == null) break;
			
			$line = str_replace("\n", "", $line);
			
			array_push($symbol_list, split_string($line, '|')[0]);
			array_push($symbol_data, $line);
			
			$open_array = array();
			$high_array = array();
			$low_array = array();
			$close_array = array();
			$volume_array = array();
			
			for ($i = 0; $i < $chart_length; $i++)
			{
				array_push($open_array, 0);
				array_push($high_array, 0);
				array_push($low_array, 0);
				array_push($close_array, 0);
				array_push($volume_array, 0);
			}
		
			
			array_push($chart_list, array($open_array, $high_array, $low_array, $close_array, $volume_array, 0));
						
		}
		
		
		
		fclose($file);
		
		
		
		
		
		
		
		$path = 'C:\Users\Russell Brown\Documents\PHP Scripts\normalized_data.dat';		
		$file = fopen($path,"r");		
		
		
		
		$output_path = 'C:\Users\Russell Brown\Documents\PHP Scripts\top_data.dat';	
		if (file_exists($output_path))
		{		
			unlink($output_path);
		}	
		
		$output_file = fopen($output_path,"a+");		
		
		$line = "";
		$line_count = 0;
		$date_count = 0;
		$read_line = "false";
		
		
		
		//-----------------------------
		
		
		
		
		while (true)
		{
			$data = fread($file, 1000000);		
		//	print(ftell($file)."   ".strlen($data)."   ".filesize($path)."\n");		
			for ($i = 0; $i < strlen($data); $i++)
			{
				if ($data[$i] == "\n")
				{		
					$arr = split_string($line, ':');
					
					
					if ($read_line == "true")
					{
						$c = 0;
						for ($i2 = 0; $i2 < strlen($line); $i2++)
						{
							if ($line[$i2] == '-')
							{
								$c++;
							}
						}
						if ($c == strlen($line))
						{
							
							if (sizeof($price_list) == 0)
							{
								if (sizeof($saved_price_list) > 0)
								{
									$price_list = $saved_price_list;
								}
							}
						
							if (sizeof($diff_array) >= 5)
							{
										
						
								// calculate top 5
								
									
								$ar = array();
																
								for ($i2 = 0; $i2 < sizeof($diff_array); $i2++)
								{
									$ar["\"".$diff_array[$i2]."\""] = $diff_array_keys[$i2];
								}
								
												
								ksort($ar);								 
								$symbol_count = 0;								 
								$output_line = $date.":  ";	
								
								foreach($ar as $key=>$val) 
								{
									$symbol = $symbol_list[$val];
									
									
									
									for ($i2 = 0; $i2 < sizeof($price_list); $i2++)
									{
										$ar2 = $price_list[$i2];
										if ($ar2[0] == $symbol)
										{
											$price2 = $ar2[1];											
											break;
										}
									}
									 									 
									if ($symbol_count >= sizeof($ar) - 5)
									{				
										$output_line = $output_line.$symbol." [".$price2." ".$key."] ";
									}
									$symbol_count++;
								 }
								 
								 
								 
								 $output_line = $output_line."\n";
								 
								 
								 fwrite($output_file, $output_line);
								
								
									
							}
							
							
							
							$read_line = "false";
							
							$date_count++;
							if ($date_count > 50)
							{
								//exit();
							}
							
						}
					}
					
					if ($read_line == "true")
					{
						$arr2 = split_string($line, ',');		
						
						$has_data = "true";
						
						for ($i2 = 0; $i2 < sizeof($arr2); $i2++)
						{
							if ($arr2[$i2] == "")
							{
								$has_data = "false";
								break;
							}
						}
						
						if ($arr2[6] < 10 || $arr2[6] > 1000 || $arr2[8] < 1000)
						{
							$has_data = "false";
						}
						
						
						if ($has_data == "true")
						{
						
							$symbol = $arr2[0];
														
							// symbol list is from file - not from date
							$id = array_search($symbol, $symbol_list);
							if ($id == true)
							{								
								$array = $chart_list[$id];
								
								$open_array = $array[0];
								
								for ($i2 = 1; $i2 < $chart_length; $i2++)
								{
									$open_array[$i2 - 1] = $open_array[$i2];
								}
								
								$open_array[$chart_length - 1] = doubleval($arr2[3]);
								
								$array[0] = $open_array;
								
								
								$high_array = $array[1];
								
								for ($i2 = 1; $i2 < $chart_length; $i2++)
								{
									$high_array[$i2 - 1] = $high_array[$i2];
								}
								
								$high_array[$chart_length - 1] = doubleval($arr2[4]);
								
								$array[1] = $high_array;
								
								
								$low_array = $array[2];
								
								for ($i2 = 1; $i2 < $chart_length; $i2++)
								{
									$low_array[$i2 - 1] = $low_array[$i2];
								}
								
								$low_array[$chart_length - 1] = doubleval($arr2[5]);
								
								$array[2] = $low_array;
								
								$close_array = $array[3];
								
								for ($i2 = 1; $i2 < $chart_length; $i2++)
								{
									$close_array[$i2 - 1] = $close_array[$i2];
								}
								
								$close_array[$chart_length - 1] = doubleval($arr2[6]);
								
								
								//---------------------------------------------------------------
								// update price list with symbol & price
								array_push($price_list, array($symbol, doubleval($arr2[6])));
								//---------------------------------------------------------------
								
								
								$array[3] = $close_array;
								
								$volume_array = $array[4];
								
								for ($i2 = 1; $i2 < $chart_length; $i2++)
								{
									$volume_array[$i2 - 1] = $volume_array[$i2];
								}
								
								$volume_array[$chart_length - 1] = intval($arr2[8]);
								
								$array[4] = $volume_array;
								
								$chart_list[$id] = $array;
								
								//----------------------------
								
								
								$count = $array[5];
								
								if ($count < $chart_length)
								{
									$count++;								
									$array[5] = $count;
									$chart_list[$id] = $array;
								}
								else
								{			
									$period = 5;
							
									$close_array = $array[3];
									
									$diff = $close_array[$chart_length - 1] - $close_array[$chart_length - $period];
								
									$diff = round($diff, 2);
									
									
									
									array_push($diff_array, $diff);
									array_push($diff_array_keys, $id);
								
								}
								
								//----------------------------
								
								
							}
						
						}
						
						
					}
					
					
					if ($arr[0] == "data for date")
					{
						$read_line = "true";
						$date = str_replace(" ", "", $arr[1]);
						
						$diff_array = array();
						$diff_array_keys = array();
						
						$saved_price_list = $price_list;
						$price_list = array();
						
						
						print $date.": ".date("h:i")."\n";
					}
										
					
					$line_count++;
				//	if ($line_count > 100000) break;
					$line = "";
				}
				else if ($data[$i] != "\r")
				{
					$line = $line.$data[$i];
				}	
						
			}
			//if ($line_count > 100000) break;
			if (feof($file) == true) break;
		}
		
		fclose($file);
		fclose($output_file);
		
		
	}
	
	
	
	
	
	

	/*
	print "downloading symbols:\n";
	print date("h:i")."\n";
	download_symbols();	
	print "done\n";
	print date("h:i")."\n";*/
	
	/*
	// note:  takes approx 1 hr and 30 mins
	print "downloading price data:\n";
	print date("h:i")."\n";
	download_price_data();
	print "done\n";
	print date("h:i")."\n";
*/
	
	/*
	print "mapping data:\n";
	print date("h:i")."\n";
	map_data();
	print date("h:i")."\n";
	*/
	

	/*
	// note:  takes approx 25 mins
	print "normalize data:\n";
	print date("h:i")."\n";
	normalize_data();	
	print "done\n";
	print date("h:i")."\n";
	*/
	
	
	
	
	
	print "calculating top gains:\n";
	print date("h:i")."\n";
	calculate_top_gains();
	print "done\n";
	print date("h:i")."\n";
	
	
	
	
	
	
?>

