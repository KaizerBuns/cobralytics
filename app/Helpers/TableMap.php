<?php namespace App\Helpers;
use App\Helpers\MyHelper;

// TABLE MAP
define('TOTAL_TOTAL', 1);
define('TOTAL_AVERAGE', 2);
define('TOTAL_RPM', 3);
define('TOTAL_RPC', 4);
define('TOTAL_CTR', 5);

class TableMap {
	public static $global_params = array(
		'table_id' => 'tblmap',
		'form_id' => 'tblmapfrm',
		'css_class' => '',
		'table_class' => '',
		'table_class_row' => '',
		'empty_message' => 'There is no data to display',
		'enable_totals' => 0,
		'enable_totals_bottom' => 0,
		'custom_totals' => array(),
	);

	public static function create_dropdown(Array $dropdown_options) {

		$dropdown = '<div class="btn-group"><a href="#" data-toggle="dropdown" class="btn dropdown-toggle">' . $dropdown_options['name'] . '&nbsp;&nbsp;<span class="caret"></span></a><ul class="dropdown-menu">';

		foreach ($dropdown_options['menu_items'] as $i) {
			$dropdown .= "<li><a href='#' onclick='{$i['onclick']}'><i class='{$i['icon']}'></i>&nbsp;&nbsp;{$i['name']}</a></li>";
		}

		if (isset($dropdown_options['menu_manage']) && $dropdown_options['menu_manage']) {
			$dropdown .= '<li class="divider"></li>';
		}
		foreach ($dropdown_options['menu_manage'] as $i) {
			$dropdown .= "<li><a href='{$i['link']}'><i class='i'></i>&nbsp;&nbsp;{$i['name']}</a></li>";
		}

		$dropdown .= '</ul></div>';
		return $dropdown;
	}

	public static function value_format($type, $value) {
		switch ($type) {
		case 'boolean':
			$value = ($value) ? "Yes" : "No";
			break;
		case 'percentage':
			$value = number_format($value, 2) . '%';
			break;
		case 'number':
			$value = number_format((int) $value);
			break;
		case 'float':
		case 'double':
			$value = number_format((float) $value, 2);
			break;
		case 'nice-date':
			if ($value) {
				$value = date("M j, Y", strtotime($value));
			}
			break;
		case 'nice-date-time':
			if ($value) {
				$value = date("M j, Y @ g:ia", strtotime($value));
			}
			break;
		case 'nice-date-time-short':
			if ($value) {
				$value = date("M j g:ia", strtotime($value));
			}
			break;
		case 'nice-time':
			if ($value) {
				$value = date("g:ia", strtotime($value));
			}
			break;
		case 'monthyear':
			$value = date("F Y", strtotime($value));
			break;
		case 'performance_percent':
		case 'performance_number':
		case 'performance_money':
			$original_value = $value;
			if ($value < 0) {
				$value = $value * -1;
			}

			$icon_class_up = '<i class="icon-chevron-sign-up"></i> ';
			$icon_class_dn = '<i class="icon-chevron-sign-down"></i> ';
			$icon_class_nc = '<i class="icon-resize-horizontal"></i> ';
			if ($type == 'performance_percent') {
				$value = $value * 100;
				$value_format = number_format($value, 1) . '%';
			} else if ($type == 'performance_money') {
				$icon_class_up = "+";
				$icon_class_dn = "-";
				$icon_class_nc = "";
				$value_format = '$' . number_format($value, 2);
			} else {
				$value_format = number_format($value, 0);
				$icon_class_up = "+";
				$icon_class_dn = "-";
				$icon_class_nc = "";
			}

			if ($original_value == 0) {
				$value = $icon_class_nc . $value_format;
			} else if ($original_value > 0) {
				$value = $icon_class_up . $value_format;
			} else if ($original_value < 0) {
				$value = $icon_class_dn . $value_format;
			}
			break;
		case 'money':
			$value = '$' . number_format($value, 2, '.', ',');
			break;
		case 'capitals':
		case 'uppercase':
			$value = strtoupper($value);
			break;
		case 'lowercase':
			$value = strtolower($value);
			break;
		case 'ucfirst':
			$value = ucfirst(strtolower($value));
			break;
		case 'ucwords':
			$value = ucwords($value);
			break;
		case 'json_encode':
			$value = json_encode($value);
			break;
		case 'json_decode_sort':
			$value = json_decode($value, true);
			ksort($value);
			$tmp = array();
			foreach ($value as $k => $v) {
				$tmp[] = "$k: $v";
			}
			$value = implode("<br>", $tmp);
			break;
		case 'json_format':
			if (json_decode($value)) {
				$value = self::decode($value);
			} else {
				$temp = explode('{', $value, 2);
				$value = nl2br($temp[0] . "\n");
				if (count($temp) > 1) {
					$value .= self::decode("{" . $temp[1]);
				}
			}
			break;
		case 'code':
			$value = "<code>" . stripslashes(htmlentities($value)) . "</code>";
			break;
		case 'display_rule':
			if ($value == '?') {
				$value = 'All';
			} else {
				$value = strtoupper($value);
			}
			break;
		case 'image_size':
			$type = 'KB';
			$size = round($value / 1000, 2);
			if ($size > 1000) {
				$size = round($size / 1000, 2);
				$type = 'MB';
			}

			$value = number_format($size, 2, '.', ',') . ' ' . $type;
			break;
		default:
			if (preg_match('/trimtext/', $type)) {
				list($type, $length) = explode("-", $type);
				$original_value = wordwrap($value, $length, '</br>', true);
				$value = self::trimtext($value, $length);
				if ($original_value != $value) {
					$value = "<span title='{$original_value}' class='cmd-tip'>{$value}</span>";
				}
			}
			break;
		}
		return $value;
	}

	public static function simple_pagination($callback_params, $params) {
		$callback_link = '';
		if (isset($callback_params['link'])) {
			$callback_link = $callback_params['link'];
			if (!strstr($callback_link, "?")) {
				$callback_link .= "?";
			}
		}

		$callback_onclick = '';
		if (isset($callback_params['onclick'])) {
			$callback_onclick = $callback_params['onclick'];
		}

		$total_rows = 0;
		$trows = '';
		if (isset($params['total_rows'])) {
			$total_rows = $params['total_rows'];
			$trows = '&total_rows=' . $total_rows;
		}

		$css_class = (isset($params['css'])) ? $params['css'] : 'paginate';
		$current_page = (isset($params['p'])) ? $params['p'] : 1;
		$limit_rows = (isset($params['limit_rows'])) ? $params['limit_rows'] : 25;
		$limit_offset = ($current_page - 1) * $limit_rows;

		$first_page = 1;
		$total_pages = $last_page = ceil($total_rows / $limit_rows);
		$next_page = ($current_page + 1) > $last_page ? $last_page : $current_page + 1;
		$previous_page = ($current_page - 1) < 1 ? $first_page : $current_page - 1;

		/*
			$pag_params = array();
			$pag_params['fp']['link'] = $callback_link .'&p=1';
			$pag_params['lp']['link'] = $callback_link .'&p='.$last_page;
			$pag_params['pp']['link'] = $callback_link .'&p='.$previous_page;
			$pag_params['np']['link'] = str_replace("{PAGE_VALUE}", $next_page, $callback_link);
		*/

		$count = $limit_rows;
		if ($total_pages == 1) {
			return '';
		} else {
			$start = ($current_page - 1) * $limit_rows;

			if ($start == 0) {
				$start = 1;
				$end = $count;
			} else {
				$end = $start + $count;
			}
		}

		$html = '<div class="pagination pagination-centered">';
		$html .= '<ul>';

		if ($current_page >= 3) {
			$start = $current_page - 2;
			$pages = $current_page + 2;
		} else {
			$start = 1;
			if ($current_page < 5) {
				$pages = $start + 4;
			} else {
				$pages = $current_page + 2;
			}
		}

		if ($pages >= $total_pages && $current_page == $total_pages) {
			$pages = $total_pages;
			$start = $current_page - 4;
		} else if ($pages > $total_pages) {
			$pages = $total_pages;
			$start = $total_pages - 4;
		}

		if ($start < 1) {
			$start = 1;
		}

		if ($current_page == $first_page) {
			$html .= '<li><a><i class="icon-step-backward icon-disabled"></a></i></li>';
			$html .= '<li><a><i class="icon-backward icon-disabled"></i></a></li>';
		} else {
			$html .= "<li><a href='{$callback_link}&p=1{$trows}'><i class='icon-step-backward icon-blue'></i></a></li>";
			$html .= "<li><a href='{$callback_link}&p={$previous_page}{$trows}'><i class='icon-backward icon-blue'></i></a></li>";
		}

		for ($i = $start; $i <= $pages; $i++) {
			if ($i == $current_page) {
				$html .= "<li class='active'><a href='javascript::void(0);'><span>{$i}</span></a></li>";
			} else {
				$html .= "<li><a href='" . $callback_link . '&p=' . $i . "{$trows}'><span>{$i}</span></a></li>";
			}
		}

		if ($current_page == $last_page) {
			$html .= "<li><a><i class='icon-forward icon-disabled'></i></a></li>";
			$html .= "<li><a><i class='icon-step-forward icon-disabled'></i></a></li>";
		} else {
			$html .= "<li><a href='{$callback_link}&p={$next_page}{$trows}'><i class='icon-forward icon-blue'></i></a></li>";
			$html .= "<li><a href='{$callback_link}&p={$last_page}{$trows}'><i class='icon-step-forward icon-blue'></i></a></li>";
		}

		$html .= '</ul>';
		$html .= '</div>';
		return $html;
	}

	/**
	 * Enter description here...
	 *
	 * @param array $data
	 * @param array $descriptor
	 * @param array $map_params
	 * AVAILABLE PARAMS
	 * css_class
	 * action_url
	 * table_id
	 * type
	 * empty_message
	 * enable_drilldown Array
	id
	trigger Array Optional
	this will trigger the drilldown when the following field => value criteria is met
	Array('source_id' => 'Multiple')
	url (method to use /members/report/ajax_expand_drilldown/)
	value_field Array
	it will send these fields as params in the url (ex id=1,email=cesarg@ytzemail.com,revenue=8)
	ex Array(id, email, revenue)
	custom_field Array
	manually set the field=>values as params in the url
	ex Array('start_date' => $start_date, 'end_date' => $end_date)
	output will be as params in the url (ex start_date=2011-04-01,end_date=2011-04-20)
	callback String (Optional)
	run this function after ajax request
	 * enable_totals
	 * enable_form Array
	 * form_id
	 * form_action
	 * form_class
	 * form_button Array
	 * name
	 * value
	 * onclick
	 * class
	 * enable_checkbox Array
	 * value_field
	 * value_selected Array
	 * custom_links Array
	 * Name
	 * image
	 * onclick
	 * enable_checkbox_bottom Array
	 * value_field
	 * value_selected Array
	 * custom_links Array
	 * Name
	 * image
	 * onclick
	 * custom_totals Array
	 * @return unknown
	 */
	public static function create($map_data = array(), Array $map_descriptor, Array $map_params) {
		$row_total = '';
		$html = '';

		//check for missing descriptor
		if (count($map_descriptor) == 0) {
			return false;
		}

		//set default params
		if (!isset($map_params['empty_message'])) {
			$map_params['empty_message'] = self::$global_params['empty_message'];
		}

		if (!isset($map_params['enable_totals'])) {
			$map_params['enable_totals'] = self::$global_params['enable_totals'];
		}

		if (!isset($map_params['datatable'])) {
			$map_params['datatable'] = false;
		}

		if (!isset($map_params['enable_totals_bottom'])) {
			$map_params['enable_totals_bottom'] = self::$global_params['enable_totals_bottom'];
		}

		if (!isset($map_params['table_id'])) {
			$map_params['table_id'] = self::$global_params['table_id'];
		}

		if (!isset($map_params['form_id'])) {
			$map_params['form_id'] = self::$global_params['form_id'];
		}

		if (!isset($map_params['table_class'])) {
			$map_params['table_class'] = self::$global_params['table_class'];
		}

		if (!isset($map_params['table_class_row'])) {
			$map_params['table_class_row'] = self::$global_params['table_class_row'];
		}

		$table_headers = true;
		if (isset($map_params['headers']) && $map_params['headers'] == false) {
			$table_headers = false;
		}

		$custom_totals = false;
		if (isset($map_params['custom_totals']) && count($map_params['custom_totals'])) {
			$custom_totals = true;
		}

		if (isset($map_params['enable_links']) && !isset($map_params['enable_checkbox'])) {
			$custom_links = array();
			if (isset($map_params['enable_links'])) {
				foreach ($map_params['enable_links'] as $custom_link) {
					$custom_html = "";
					if (isset($custom_link['image'])) {
						$custom_html .= '<img src="' . $custom_link['image'] . '" style="vertical-align:middle"> ' . $custom_link['name'];
					} else {
						if (isset($custom_link['name'])) {
							$custom_html .= $custom_link['name'];
						}
					}
					if (isset($custom_link['html'])) {
						$custom_html .= $custom_link['html'];
					}

					if (isset($custom_link['onclick'])) {
						$custom_html = '<a href="javascript:void(0);" onclick="' . $custom_link['onclick'] . '">' . $custom_html . '</a>';
					}

					$custom_links[] = $custom_html;
				}
			}

			$html .= '<div class="enable_links">' . implode('</div><div class="enable_item_links">', $custom_links) . '</div>';
		}

		$html .= '<table id="' . $map_params['table_id'] . '" cellpadding="0" cellspacing="0" class="table table-striped table-hover table-condensed ' . $map_params['table_class'] . '" width="100%">';

		if ($table_headers) {
			$html .= '<thead>';
			if (isset($map_params['enable_checkbox']) && !isset($map_params['enable_links'])) {
				$html .= '<tr class="check_all">';
				$html .= '<td colspan=' . (count($map_descriptor) + 2) . '>';

				$custom_links = array();
				if (isset($map_params['enable_checkbox']['custom_links'])) {
					foreach ($map_params['enable_checkbox']['custom_links'] as $custom_link) {
						$custom_html = "";
						if (isset($custom_link['image'])) {
							$custom_html .= '<img src="' . $custom_link['image'] . '" style="vertical-align:middle"> ' . $custom_link['name'];
						} else {
							if (isset($custom_link['name'])) {
								$custom_html .= $custom_link['name'];
							}
						}

						if (isset($custom_link['html'])) {
							$custom_html .= $custom_link['html'];
						}

						if (isset($custom_link['onclick'])) {
							$custom_html = '<a href="javascript:void(0);" onclick="' . $custom_link['onclick'] . '">' . $custom_html . '</a>';
						}

						$custom_links[] = $custom_html;
					}
				}

				$html .= '<span><input type="checkbox" style="padding:0px; margin:0px;" value="0" id="' . $map_params['table_id'] . '_chkall" name="' . $map_params['table_id'] . '_chkall">&nbsp;&nbsp;</span><span id="' . $map_params['table_id'] . '_checkall_count" class="checkbox_count"></span>';
				$html .= '<span>' . implode('</span><span style="float:left; padding-right:5px;">', $custom_links) . '</span>';
				$html .= '</td>';
				$html .= '</tr>';
			}

			$html .= '<tr>';
			if (isset($map_params['enable_drilldown'])) {
				$html .= '<th class="drilldown">&nbsp;</th>';
			}

			if (isset($map_params['enable_checkbox'])) {
				$html .= '<th>&nbsp;</th>';
			}

			foreach ($map_descriptor as $name => $descriptor) {
				if (isset($descriptor['html']['title'])) {
					$title_class = ' <a title="' . $descriptor['html']['title'] . '" class="cmd-tip" style="cursor:pointer"><i class="icon-info"></i></a>';
				} elseif (isset($descriptor['title'])) {
					$title_class = ' <a title="' . $descriptor['title'] . '" class="cmd-tip" style="cursor:pointer"><i class="icon-info"></i></a>';
				} else {
					$title_class = '';
				}

				$html .= '<th>';
				//$html.= '<i class="ace-icon fa fa-caret-right blue"></i>&nbsp;';

				if (isset($descriptor['not_sortable']) || isset($map_params['not_sortable'])) {
					$html .= $name . $title_class;
				} else {
					$sort = (isset($descriptor['sort_field']) && $descriptor['sort_field'] ? $descriptor['sort_field'] : (isset($descriptor['field']) ? $descriptor['field'] : $name));
					$add_form = '';
					if ($map_params['form_id']) {
						$add_form = '\' + $(\'' . $map_params['form_id'] . '\').serialize() + \'';
					}

					//check action url
					if (isset($map_params['action_url'])) {
						$sort_icon[$sort] = '';
						if (isset($_REQUEST['order'])) {
							if ($_REQUEST['order'] == 1) {
								$sort_icon[$_REQUEST['sort']] = "<i class='fa fa-caret-down'></i>";
							} else {
								$sort_icon[$_REQUEST['sort']] = "<i class='fa fa-caret-up'></i>";
							}
						}

						$sort_order[$sort] = 1;
						if (isset($_REQUEST['sort']) && isset($_REQUEST['order']) && $_REQUEST['sort'] == $sort) {
							$sort_order[$sort] = ($_REQUEST['order'] == 1 ? 0 : 1);
						}

						//its a url
						if (preg_match("/\//", $map_params['action_url'])) {
							/*
								$url = parse_url($map_params['action_url']);
								if (isset($url['query'])) {
									parse_str(urldecode($url['query']), $url_array);
									unset($url_array['sort']);
									unset($url_array['order']);
									$map_params['action_url'] = $url['path'].'?'.http_build_query($url_array);
								} else {
									$map_params['action_url'] = $url['path'].'?';
							*/

							$url_page = MyHelper::page_url(true);
							$html .= $sort_icon[$sort] . '&nbsp;<a href="#sort_by_' . $sort . '" onclick="document.location = \'' . $url_page . '&sort=' . $sort . '&order=' . $sort_order[$sort] . '\'">' . $name . '</a>' . $title_class;
						} else {
							//set your own custom replacements
							$new_action_url = str_replace("%%sort%%", $sort, $map_params['action_url']);
							$new_action_url = str_replace("%%order%%", $sort_order[$sort], $new_action_url);
							$html .= $sort_icon[$sort] . '<a href="javascript:void(0);" onclick="' . $new_action_url . '">' . $name . '</a>' . $title_class;
						}
					} else {
						$html .= $name . $title_class;
					}
				}
				$html .= '</th>';
			}
			$html .= '</tr>';
			$html .= '</thead>';
		}

		if ($map_params['enable_totals'] || $map_params['enable_totals_bottom']) {
			$totals = array();
			if ($custom_totals) {
				$totals = $map_params['custom_totals'];
			} else {
				foreach ((array) $map_data as $key => $td) {
					foreach ($map_descriptor as $name => $descriptor) {
						if (!isset($totals[$name])) {
							$totals[$name] = 0;
						}

						if (isset($map_params['enable_totals']) || isset($map_params['enable_totals_bottom'])) {
							if (is_object($td)) {
								$totals[$name] += $td->{$descriptor['field']};
							} else {
								$totals[$name] += $td[$descriptor['field']];
							}
						}
					}
				}
			}

			$count = 0;
			$row_total = '<tr style="font-weight:bold; color:#ED3A00 !important">';

			if (isset($map_params['enable_checkbox']) && isset($map_params['enable_drilldown'])) {
				$row_total .= '<td colspan="2">TOTALS</td>';
			} else {
				if (isset($map_params['enable_checkbox']) || isset($map_params['enable_drilldown'])) {
					$row_total .= '<td>TOTALS</td>';
				}
			}

			foreach ($map_descriptor as $name => $descriptor) {
				if ($custom_totals && isset($descriptor['custom_totals'])) {
					$name = $descriptor['custom_totals'];
				}

				if (!isset($totals[$name])) {
					$totals[$name] = 0;
				}

				if ((!isset($map_params['enable_totals']) || !$map_params['enable_totals'] || !isset($map_params['enable_totals_bottom']) || !$map_params['enable_totals_bottom'])
					&& $count == 0) {
					$row_total .= '<td class="enabled_total">';
					if (!isset($map_params['enable_checkbox']) && !isset($map_params['enable_drilldown'])) {
						$row_total .= 'TOTALS';
					}
				} else {
					$row_total .= '<td class="enabled_total_items">';
					if (isset($descriptor['if_empty']) && !$totals[$name] && (isset($map_params['enable_totals']) || isset($map_params['enable_totals_bottom']))) {
						$row_total .= $descriptor['if_empty'];
					} elseif ((!isset($descriptor['enable_totals']) || !isset($map_params['enable_totals']) || !$map_params['enable_totals']) && (!isset($map_params['enable_totals_bottom']) || !$map_params['enable_totals_bottom'])) {
						$row_total .= '&nbsp;';
					} else {
						if (!isset($map_params['enable_totals_bottom'])) {
							$map_params['enable_totals_bottom'] = null;
						}

						if (isset($totals[$name]) && !$custom_totals) {
							if ($descriptor['calc_totals'] == TOTAL_AVERAGE) {
								$totals[$name] = number_format($totals[$name] / count($map_data), 2);
							} elseif ($descriptor['calc_totals'] == TOTAL_RPM) {
								$totals[$name] = $totals['Visitors'] ? $totals['Revenue'] / $totals['Visitors'] * 1000 : 0;
							} elseif ($descriptor['calc_totals'] == TOTAL_PPM) {
								$totals[$name] = $totals['Visitors'] ? $totals['Payout'] / $totals['Visitors'] * 1000 : 0;
							} elseif ($descriptor['calc_totals'] == TOTAL_ECPM) {
								$totals[$name] = $totals['Impressions'] ? $totals['Revenue'] / $totals['Impressions'] * 1000 : 0;
							} elseif ($descriptor['calc_totals'] == TOTAL_CPM) {
								$totals[$name] = $totals['Impressions'] ? $totals['Payout'] / $totals['Impressions'] * 1000 : 0;
							} elseif ($descriptor['calc_totals'] == TOTAL_RPC) {
								$totals[$name] = $totals['Clicks'] ? $totals['Revenue'] / $totals['Clicks'] : 0;
							} elseif ($descriptor['calc_totals'] == TOTAL_CONVERSION_RATE) {
								$totals[$name] = $totals['Clicks'] ? ($totals['Actions'] / $totals['Clicks']) * 100 : 0;
							} elseif ($descriptor['calc_totals'] == TOTAL_CTR) {
								if ($totals['Visitors']) {
									$totals[$name] = $totals['Clicks'] ? ($totals['Clicks'] / $totals['Visitors']) * 100 : 0;
								} else {
									$totals[$name] = 0;
								}
							}
						}

						if (isset($descriptor['format'])) {
							$value = self::value_format($descriptor['format'], $totals[$name]);
						} else {
							$chkVal = $totals[$name];
							if ((int) $chkVal > 0 && (int) $chkVal == intval($totals[$name])) {
								$value = self::value_format('number', $totals[$name]);
							} else if (is_float($chkVal)) {
								$value = self::value_format('float', $totals[$name]);
							} else {
								$value = $totals[$name];
							}
						}

						$row_total .= $value;
					}
				}

				$row_total .= '</td>';
				$count++;
			}
			$row_total .= '</tr>';

			if ($map_params['enable_totals'] && !$map_params['datatable']) {
				$html .= $row_total; // copy the data to the html
			} else {
				if ($map_params['datatable']) {
					$html .= '<tfoot style="text-align:left;">';
					$html .= $row_total;
					$html .= "</tfoot>";
				}
			}
		}

		$html .= '<tbody>';

		$count = 0;
		if (count($map_data) != 0) {
			$chk_added = false;
			foreach ((array) $map_data as $td) {
				$id = 0;

				if (is_object($td)) {
					$id = (isset($td->id) ? $td->id : 0);
				} else {
					$id = (isset($td['id']) ? $td['id'] : 0);
				}

				$row_id = 'tblmap_' . ($id ? $id : $count);
				if ($count % 2 == 0) {
					$class = "even";
				} else {
					$class = "odd";
				}

				//check disabled field (this will disabled any actions
				$disable = 0;
				if (is_object($td)) {
					if (isset($map_params['disable_field']) && $td->{$map_params['disable_field']}) {
						$disable = 1;
					}
				} else {
					if (isset($map_params['disable_field']) && $td[$map_params['disable_field']]) {
						$disable = 1;
					}
				}

				$dynamic_class = '';
				if (isset($map_params['dynamic_row_class'])) {
					$replacers = array();
					$replacements = array();
					foreach ($map_params['dynamic_row_class']['values'] as $replacer => $value_fields) {
						$field = $value_fields;
						$replacers[] = '{' . strtoupper($replacer) . '}';
						if (is_object($td)) {
							$replacement = $td->{$field};
						} else {
							$replacement = $td[$field];
						}

						$replacements[] = $replacement;
					}

					$dynamic_class = str_replace($replacers, $replacements, $map_params['dynamic_row_class']['template']);
				}

				$html .= '<tr class="' . $class . ' ' . $dynamic_class . '" id="' . $row_id . '">';

				if (isset($map_params['enable_drilldown'])) {
					$params = array();
					foreach ((array) $map_params['enable_drilldown']['value_field'] as $value_field) {
						if (isset($td->$value_field) || isset($td[$value_field])) {
							if (is_object($td)) {
								$params[] = $value_field . '=' . $td->{$value_field};
							} else {
								$params[] = $value_field . '=' . $td[$value_field];
							}
						}
					}

					foreach ((array) $map_params['enable_drilldown']['custom_field'] as $key_field => $value_field) {
						$params[] = $key_field . '=' . $value_field;
					}

					$params = implode('&', $params);
					$onclick = "tblmap_drilldown('{$map_params['enable_drilldown']['id']}', {$count}, '{$map_params['enable_drilldown']['url']}', '{$params}');";

					if (isset($map_params['enable_drilldown']['callback']) && $map_params['enable_drilldown']['callback'] != '') {
						$onclick .= "{$map_params['enable_drilldown']['callback']}('$params');";
					}

					if (isset($map_params['enable_drilldown']['css_class'])) {
						$html .= '<td class="' . $map_params['enable_drilldown']['css_class'] . '">';
					} else {
						$html .= '<td class="slim_col">';
					}

					$show_drilldown = true;
					if (isset($map_params['enable_drilldown']['trigger'])) {
						$trigger_column = $map_params['enable_drilldown']['trigger']['name'];
						$trigger_value = $map_params['enable_drilldown']['trigger']['value'];

						if (isset($td[$trigger_column])) {
							if ($td[$trigger_column] == $trigger_value) {
								$show_drilldown = true;
							} else {
								$show_drilldown = false;
							}
						} else {
							$show_drilldown = false;
						}
					}

					if ($show_drilldown) {
						$tooltip = '';
						if (isset($map_params['enable_drilldown']['tooltip'])) {
							$tooltip = ' title="' . $map_params['enable_drilldown']['tooltip'] . '"';
						}
						$html .= '<a href="javascript:void(0);" onclick="' . $onclick . '" ' . $tooltip . '><span id="' . $map_params['enable_drilldown']['id'] . '_img_' . $count . '"><i class="icon-plus-sign-alt"></i></span></a>';
					} else {
						$html .= '&nbsp;';
					}
					$html .= '</td>';
				}

				if (isset($map_params['enable_checkbox'])) {
					if (is_object($td)) {
						$chk_value = $td->{$map_params['enable_checkbox']['value_field']};
					} else {
						$chk_value = $td[$map_params['enable_checkbox']['value_field']];
					}

					$chk_selected = "";
					if (isset($map_params['enable_checkbox']['value_selected'])) {
						if (in_array($chk_value, $map_params['enable_checkbox']['value_selected'])) {
							$chk_selected = "checked";
						}
					}

					$html .= '<td style="width:25px; padding-left:16px;">';
					$html .= '<input type="checkbox" ' . ($disable ? 'disabled' : '') . ' class="' . $map_params['table_id'] . '_chkbox_selector" name="' . $map_params['table_id'] . '_chkbox[]" id="' . $map_params['table_id'] . '_chkbox_' . $chk_value . '" value="' . $chk_value . '" ' . $chk_selected . '>';
					$html .= '</td>';
				}

				$inline_js = false;
				foreach ($map_descriptor as $name => $descriptor) {
					$field_id = $row_id . "_" . strtolower($name);
					$css_class = $map_params['table_class_row'];

					if (isset($descriptor['html']['class'])) {
						$css_class = $descriptor['html']['class'];
					} elseif (isset($descriptor['class'])) {
						$css_class = $descriptor['class'];
					}

					$html .= "<td class='{$css_class}'>";
					if (isset($descriptor['field']) && $descriptor['field']) {
						if (is_object($td)) {
							$value = $td->{$descriptor['field']};
						} else {
							$value = $td[$descriptor['field']];
						}
					}

					if (isset($descriptor['replace'])) {
						$search = $descriptor['replace']['search'];
						$replace = $descriptor['replace']['replace'];
						$value = str_replace($search, $replace, $value);
					}

					if (isset($descriptor['field'])) {
						if (isset($descriptor['if_empty']) && !trim($value)) {
							$value = $descriptor['if_empty'];
						} else {
							//get the unique ID
							if (isset($descriptor['format'])) {
								$value = self::value_format($descriptor['format'], $value);
							} else {
								$chkVal = $value;
								if (is_int($chkVal) && (int) $chkVal > 0 && (int) $chkVal == intval($chkVal)) {
									$value = self::value_format('number', $value);
								} elseif (is_float($chkVal)) {
									$value = self::value_format('float', $value);
								}
							}

							//value before it gets html added
							$original_value = $value;
							if (isset($descriptor['linkto'])) {
								if (is_object($td)) {
									$link_value = $td->{$descriptor['linkto']['value_field']};
								} else {
									$link_value = $td[$descriptor['linkto']['value_field']];
								}

								$link_class = '';
								if (isset($descriptor['linkto']['class'])) {
									$link_class = "class=\"{$descriptor['linkto']['class']}\"";
								}

								$link_target = '';
								if (isset($descriptor['linkto']['target'])) {
									$link_target = "target=\"{$descriptor['linkto']['target']}\"";
								}

								$url = str_replace('{VALUE}', $link_value, $descriptor['linkto']['url']);
								$value = '<a href="' . $url . '" ' . $link_class . ' id="' . $field_id . '_inline_text" ' . $link_target . '>' . $value . '</a>';
							} else {
								$value = '<span id="' . $field_id . '_inline_text">' . $value . '</span>';
							}

							if (isset($descriptor['inline_edit'])) {
								//Check if theres a disabled field
								if (!$disable) {
									if (isset($descriptor['inline_edit']['condition_field'])) {
										if (is_object($td)) {
											$condition_value = $td->{$descriptor['inline_edit']['condition_field']};
										} else {
											$condition_value = $td[$descriptor['inline_edit']['condition_field']];
										}

										if (isset($descriptor['inline_edit']['condition_cases'][$condition_value])) {
											$descriptor['inline_edit'] = $descriptor['inline_edit']['condition_cases'][$condition_value];
										}
									}

									$field_name = 'fieldName';
									if (isset($descriptor['inline_edit']['name'])) {
										$field_name = $descriptor['inline_edit']['name'];
									}

									$field_model = 'Model';
									if (isset($descriptor['inline_edit']['model'])) {
										$field_model = $descriptor['inline_edit']['model'];
									}

									$field_class = '';
									if (isset($descriptor['inline_edit']['class'])) {
										$field_class = $descriptor['inline_edit']['class'];
									}

									$field_validate = false;
									if (preg_match("/validate/", $field_class)) {
										$field_validate = true;
									}

									$field_params = array();
									if (isset($descriptor['inline_edit']['params'])) {
										$field_params = $descriptor['inline_edit']['params'];
									}

									$field_select_values = array();
									if (isset($descriptor['inline_edit']['values'])) {
										$field_select_values = $descriptor['inline_edit']['values'];
									}

									$field_selected_value = $original_value;
									if (isset($descriptor['inline_edit']['value_field'])) {
										$selected_field = $descriptor['inline_edit']['value_field'];
										if (is_object($td)) {
											$field_selected_value = $td->{$selected_field};
										} else {
											$field_selected_value = $td[$selected_field];
										}
									}

									//PARAMS FOR THE AJAX URL
									$url_params = array();
									foreach ($field_params as $name) {
										if (is_object($td)) {
											$param_value = $td->$name;
										} else {
											$param_value = $td[$name];
										}

										$url_params[] = "params[$name]=$param_value";
									}

									$edit_element_id = $field_id . '_inline_element';
									$edit_url = (isset($descriptor['inline_edit']['url']) ? $descriptor['inline_edit']['url'] : '');
									$edit_params = implode("&", $url_params);

									// ADD LINK TO FIELD
									if (isset($descriptor['inline_edit']['add_link'])) {
										if (is_object($td)) {
											$link_value = $td->$descriptor['inline_edit']['value_field'];
										} else {
											$link_value = $td[$descriptor['inline_edit']['value_field']];
										}

										$link_class = '';
										if (isset($descriptor['linkto']['class'])) {
											$link_class = "class=\"{$descriptor['inline_edit']['add_link']['class']}\"";
										}

										$link_target = '';
										if (isset($descriptor['linkto']['target'])) {
											$link_target = "target=\"{$descriptor['inline_edit']['add_link']['target']}\"";
										}

										$url = str_replace('{VALUE}', $link_value, $descriptor['inline_edit']['add_link']['url']);
										$value = '<a href="' . $url . '" ' . $link_class . ' id="' . $field_id . '_inline_text" ' . $link_target . '>' . $value . '</a>';
									} else {
										$value = '<span id="' . $field_id . '_inline_text">' . $value . '</span>';
									}
									if (isset($descriptor['inline_edit']['value_field'])) {
									}
									// INLINE CALLBACKS WHEN CLICK EDIT
									$inline_js_edit_callbacks = '';
									if (isset($descriptor['inline_edit']['edit_callback'])) {
										$callback = $descriptor['inline_edit']['edit_callback'];

										$callback_function = $callback['function'];
										$callback_params = (array) $callback['params'];

										$params = array();
										foreach ($callback_params as $name) {
											if (is_object($td)) {
												$map_value = $td->$name;
											} else {
												$map_value = $td[$name];
											}

											$params[] = $map_value;
										}

										$inline_js_edit_callbacks = "{$callback['function']}('{$row_id}','" . implode("','", $params) . "');";
									}

									// INLINE CALLBACKS WHEN CLICK CANCEL
									$inline_js_cancel_callbacks = 'tblmap_inline_toggle(\'' . $field_id . '\');';
									if (isset($descriptor['inline_edit']['cancel_callback'])) {
										$callback = $descriptor['inline_edit']['cancel_callback'];

										$callback_function = $callback['function'];
										$callback_params = (array) $callback['params'];

										$params = array();
										foreach ($callback_params as $name) {
											if (is_object($td)) {
												$map_value = $td->$name;
											} else {
												$map_value = $td[$name];
											}

											$params[] = $map_value;
										}

										$inline_js_cancel_callbacks = "{$callback['function']}('{$row_id}','" . implode("','", $params) . "');";
									}

									// INLINE CALLBACKS WHEN CLICK SAVE
									$inline_js_save_callbacks = "";
									if (isset($descriptor['inline_edit']['save_callback'])) {
										$callback = $descriptor['inline_edit']['save_callback'];

										$callback_function = $callback['function'];
										$callback_params = (array) $callback['params'];

										$params = array();
										foreach ($callback_params as $name) {
											if (is_object($td)) {
												$map_value = $td->$name;
											} else {
												$map_value = $td[$name];
											}

											$params[] = $map_value;
										}

										$inline_js_save_callbacks = "{$callback['function']}('{$row_id}','" . implode("','", $params) . "');";
									}

									// INLINE FUNCTION WHEN CLICK SAVE
									$inline_js_save_function = '';
									if (isset($descriptor['inline_edit']['save_function'])) {
										$callback = $descriptor['inline_edit']['save_function'];

										$callback_function = $callback['function'];
										$callback_params = (array) $callback['params'];

										$params = array();
										foreach ($callback_params as $name) {
											if (is_object($td)) {
												$map_value = $td->{$name};
											} else {
												$map_value = $td[$name];
											}

											$params[] = $map_value;
										}

										$inline_js_save_function = "{$callback['function']}('{$row_id}',$('#{$edit_element_id}').val(),'" . implode("','", $params) . "');";
									} else {
										$inline_js_save_function = 'tblmap_inline_save(\'' . $field_id . '\',\'' . $field_name . '\',\'' . $field_model . '\',\'' . $edit_url . '\',\'' . $edit_params . '\');';
									}

									$default_text = '<div id="' . $field_id . '_inline">' . $value . ' <a href="javascript:void(0);" onclick="tblmap_inline_toggle(\'' . $field_id . '\');' . $inline_js_edit_callbacks . '"><i class="icon-edit"></i></a></div>';
									$edit_icons = '<a href="javascript:void(0);" onClick="if(tblmap_inline_validate(\'' . $field_id . '\',\'' . $field_validate . '\')){' . $inline_js_save_function . $inline_js_save_callbacks . '}"><i class="icon-save"></i></a> <a href="javascript:void(0);" onclick="' . $inline_js_cancel_callbacks . '"><i class="icon-remove"></i></a>';

									$inline_edit = '<div id="' . $field_id . '_inline_edit" style="display:none;">';
									switch ($descriptor['inline_edit']['type']) {
									case 'text':
										$inline_edit .= '<input type="text" id="' . $edit_element_id . '" value="' . $original_value . '" class="' . $field_class . '"> ' . $edit_icons;
										break;
									case 'textarea':
										$inline_edit .= '<textarea id="' . $edit_element_id . '" class="' . $field_class . '">' . $original_value . '</textarea> ' . $edit_icons;
										break;
									case 'select':
										$inline_edit .= '<select id="' . $edit_element_id . '" class="' . $field_class . '">';
										foreach ($field_select_values as $k => $v) {
											$selected = '';
											if ($k == $field_selected_value) {
												$selected = 'selected';
											}
											$inline_edit .= '<option value="' . $k . '" ' . $selected . '>' . ucwords($v) . '</option>';
										}
										$inline_edit .= '</select> ' . $edit_icons;
										break;
									}

									$inline_edit .= '</div>';
									$value = $default_text . $inline_edit;
									$inline_js = true;
								}
							}
						}

						$html .= $value;
					} elseif (isset($descriptor['html'])) {
						if (isset($descriptor['html']['value_cases']) && is_array($descriptor['html']['value_cases'])) {
							foreach ($descriptor['html']['value_cases'] as $field => $value) {
								if (is_object($td)) {
									$descriptor['html']['html'] = $value[$td->{$field}];
								} else {
									$descriptor['html']['html'] = $value[$td[$field]];
								}
							}
						}

						if (is_array($descriptor['html']['value_field'])) {
							$format = (isset($descriptor['html']['format']) ? $descriptor['html']['format'] : '');
							$replacers = array();
							$replacements = array();
							foreach ($descriptor['html']['value_field'] as $replacer => $value_fields) {
								$format = '';
								if (is_array($value_fields) && count($value_fields)) {
									$field = $value_fields['field'];
									$format = (isset($value_fields['format']) ? $value_fields['format'] : '');
								} else {
									$field = $value_fields;
								}
								$replacers[] = '{' . strtoupper($replacer) . '}';
								if (is_object($td)) {
									$replacement = $td->{$field};
								} else {
									$replacement = $td[$field];
								}

								if ($replacement) {
									$replacements[] = self::value_format($format, $replacement);
								} else {
									if (is_numeric($replacement)) {
										$replacements[] = self::value_format($format, $replacement);
									} else {
										$replacements[] = '';
									}
								}
							}

							//Check disable flag
							if (isset($disable) && $disable) {
								if (isset($descriptor['html']['disable_html']) && $descriptor['html']['disable_html']) {
									$html .= $descriptor['html']['disable_html'];
								} else {
									$html .= '&nbsp;';
								}
							} else {

								//$replacements = array_remove_empty($replacements);
								if (count($replacements)) {
									$html_replace = str_replace($replacers, $replacements, $descriptor['html']['html']);
								} else {
									$html_replace = $descriptor['html']['if_empty'];
								}

								//CSS PERFORMANCE FIELD
								if (isset($descriptor['html']['css_performance_field']) && $descriptor['html']['css_performance_field']) {
									$performance_field = $descriptor['html']['css_performance_field'];
									if (is_object($td)) {
										$pfv = $td->{$performance_field};
									} else {
										$pfv = $td[$performance_field];
									}

									$pfv = number_format($pfv, 2);
									$color = '#5E5E5E';
									$weight = 'normal';

									if ($pfv > 0.00) {
										$color = '#57A300';
										$weight = 'normal';

										if ($pfv >= 0.01 && $pfv < 0.06) {
											$color = '#4A8C00';
										} else if ($pfv >= 0.06) {
											$color = 'green';
											$weight = 'bold';
										}
									} else if ($pfv < 0.00) {
										$pfv = $pfv * -1;
										$color = '#5E5E5E';
										$weight = 'normal';
										if ($pfv >= 0.01 && $pfv <= 0.06) {
											$color = 'red';
											$weight = 'normal';
										} else if ($pfv > 0.06) {
											$color = '#960000';
											$weight = 'bold';
										}
									}

									$html_replace = "<span style='color: {$color}; font-weight: {$weight}'>" . $html_replace . "</span>";
								}
								$html .= $html_replace;
							}
						} else if ($descriptor['html']['value_field']) {
							if (is_object($td)) {
								$html_value = $td->{$descriptor['html']['value_field']};
							} else {
								$html_value = $td[$descriptor['html']['value_field']];
							}
							//Check disable flag
							if (isset($disable) && $disable) {
								if (isset($descriptor['html']['disable_html']) && $descriptor['html']['disable_html']) {
									$html .= $descriptor['html']['disable_html'];
								} else {
									$html .= '&nbsp;';
								}
							} else {
								$html .= str_replace('{VALUE}', $html_value, $descriptor['html']['html']);
							}
						}
					} elseif (isset($descriptor['run_method'])) {
						if (is_object($td)) {
							$html .= $td->{$descriptor['run_method']['method']()};
						} else {
							$html .= $descriptor['run_method']['method']();
						}
					} else {
						$html .= '&nbsp;';
					}

					$html .= '</td>';
				}
				$html .= '</tr>';

				if (isset($map_params['enable_drilldown'])) {
					if ($show_drilldown) {
						$html .= "<tr id=\"{$map_params['enable_drilldown']['id']}_{$count}\" class=\"drilldown\" style='display:none'>";
						$html .= "<td></td>";
						$html .= "<td colspan=\"100\">";
						$html .= "<div id=\"{$map_params['enable_drilldown']['id']}_div_{$count}\"></div>";
						$html .= "</td>";
						$html .= "</tr>";
					}
				}

				$count++;
			}
		} else {
			$html .= '<tr><td colspan="' . (count($map_descriptor) + 1) . '">' . $map_params['empty_message'] . '</td></tr>';
		}

		if ($map_params['enable_totals_bottom'] && !$map_params['datatable']) {
			$html .= $row_total;
		}

		$html .= '</tbody>';

		if (isset($map_params['enable_checkbox_bottom']) && !isset($map_params['enable_links'])) {
			$bottom_custom_links = array();
			if (isset($map_params['enable_checkbox_bottom']['custom_links'])) {
				foreach ($map_params['enable_checkbox_bottom']['custom_links'] as $bottom_custom_link) {
					$bottom_custom_html = "";
					if (isset($bottom_custom_link['image'])) {
						$bottom_custom_html .= '<img src="' . $bottom_custom_link['image'] . '" style="vertical-align:middle"> ' . $bottom_custom_link['name'];
					} else {
						if (isset($bottom_custom_link['name'])) {
							$bottom_custom_html .= $bottom_custom_link['name'];
						}
					}
					if (isset($bottom_custom_link['html'])) {
						$bottom_custom_html .= $bottom_custom_link['html'];
					}

					if (isset($bottom_custom_link['onclick'])) {
						$bottom_custom_html = '<a href="javascript:void(0);" onclick="' . $bottom_custom_link['onclick'] . '">' . $bottom_custom_html . '</a>';
					}

					$bottom_custom_links[] = $bottom_custom_html;
				}
			}
			//if (count($map_data) >= 15) {

			$html .= '<tr class="check_all_bottom">';
			$html .= '<td colspan=' . (count($map_descriptor) + 2) . '>';
			$html .= '<div class="checkbox_all_up"><input type="checkbox" value="0" id="' . $map_params['table_id'] . '_bottom_chkall" name="' . $map_params['table_id'] . '_bottom_chkall"/></div>';
			$html .= '<div class="checkbox_links">' . implode('</div><div class="checkbox_link_items">', $bottom_custom_links) . '</div><div id="' . $map_params['table_id'] . '_checkall_count_bottom" class="checkbox_count"></div>';
			$html .= '</td>';
			$html .= '</tr>';

			//}
		}

		$html .= '</table>';

		if (isset($map_params['enable_form']) && $map_params['enable_form']) {
			$html_form = '<form method="POST" ';

			$html_form_bits = array();
			if (isset($map_params['enable_form']['form_action'])) {
				$html_form_bits[] = 'action="' . $map_params['enable_form']['form_action'] . '"';
			}

			if (isset($map_params['enable_form']['form_id'])) {
				$html_form_bits[] = 'id="' . $map_params['enable_form']['form_id'] . '"';
			}

			if (isset($map_params['enable_form']['form_class'])) {
				$html_form_bits[] = 'class="' . $map_params['enable_form']['form_class'] . '"';
			}

			$html_form .= implode(" ", $html_form_bits);
			$html_form .= '>';

			if (isset($map_params['enable_form']['form_hidden_fields'])) {
				foreach ($map_params['enable_form']['form_hidden_fields'] as $field) {
					$html_form .= $field['html'];
				}
			}

			$html_form .= $html;
			$html_form .= '<div>';

			if (isset($map_params['enable_form']['form_button'])) {
				$html_form .= '<input type="submit" class="' . $map_params['enable_form']['form_button']['class'] . '" name="' . $map_params['enable_form']['form_button']['name'] . '" value="' . $map_params['enable_form']['form_button']['value'] . '" onclick="' . $map_params['enable_form']['form_button']['onclick'] . '">';
			}

			$html_form .= '</div>';
			$html_form .= '</form>';

			$html = $html_form;
		}

		if (isset($map_params['enable_drilldown'])) {
			$html .= '<script type="text/javascript" charset="utf-8">';
			$html .= '
			tblmap_drilldown = function(id, count, url, params)
			{
				var row_id = id + "_" + count;
				var div_id = id + "_div_" + count;
				var img_id = id + "_img_" + count;
				params = params + "&dpid=" + count;

				if(!$("#" + row_id).is(":visible"))
				{
					$("#" + div_id).html(\'<img src="/assets/img/ajax-loader.gif" alt="Wait" />\');
					$("#" + row_id).show();
					$("#" + div_id).load(url + "?" + params);
					$("#" + img_id).html(\'<i class="icon-minus-sign-alt"></i>\');
				} else {
					$("#" + row_id).hide();
					$("#" + img_id).html(\'<i class="icon-plus-sign-alt"></i>\');
				}
			}
			';
			$html .= '</script>';
		}

		if (isset($map_params['enable_checkbox'])) {
			$html .= '<script type="text/javascript" charset="utf-8">';

			$html .= '
			$(function () {
				var count = 0;
				$("#' . $map_params['table_id'] . '_checkall_count").html(count + \' items selected\');
				$("#' . $map_params['table_id'] . '_checkall_count_bottom").html(count + \' items selected\');
				$("#' . $map_params['table_id'] . '_chkall").click(function(){
					count = 0;
					if ($(this).is(":checked")) {
						$("#' . $map_params['table_id'] . '_bottom_chkall").prop("checked", true);
						$(\'input:checkbox[name="' . $map_params['table_id'] . '_chkbox[]"]\').each(function(index) {
							if (!$(this).is(":disabled")) {
								$(this).prop("checked", true);
								count++;
							}
						});
					} else {
						$("#' . $map_params['table_id'] . '_bottom_chkall").attr("checked", false);
						$(\'input:checkbox[name="' . $map_params['table_id'] . '_chkbox[]"]\').each(function(index) {
							if (!$(this).is(":disabled")) {
								$(this).prop("checked", false);
								if(count > 0) {
									count--;
								}
							}
						});
					}

					$("#' . $map_params['table_id'] . '_checkall_count").html(count + \' items selected\');
					$("#' . $map_params['table_id'] . '_checkall_count_bottom").html(count + \' items selected\');
				});


				$(\'input:checkbox[name="' . $map_params['table_id'] . '_chkbox[]"]\').click(function(){
				 	if($(this).is(":checked")) {
				 		count++;
				 	} else {
						count--;
				 	}
					$("#' . $map_params['table_id'] . '_checkall_count").html(count + \' items selected\');
					$("#' . $map_params['table_id'] . '_checkall_count_bottom").html(count + \' items selected\');
				});

				$("#' . $map_params['table_id'] . '_bottom_chkall").click(function(){
					count = 0;
					if ($(this).is(":checked")) {
						$("#' . $map_params['table_id'] . '_chkall").attr("checked", true);
						$(\'input:checkbox[name="' . $map_params['table_id'] . '_chkbox[]"]\').each(function(index) {
							if (!$(this).is(":disabled")) {
								$(this).prop("checked", true);
								count++;
							}
						});
					} else {
						$("#' . $map_params['table_id'] . '_chkall").attr("checked", false);
						$(\'input:checkbox[name="' . $map_params['table_id'] . '_chkbox[]"]\').each(function(index) {
							if (!$(this).is(":disabled")) {
								$(this).prop("checked", false);
								if(count > 0) {
									count--;
								}
							}
						});
					}

					$("#' . $map_params['table_id'] . '_checkall_count").html(count + \' items selected\');
					$("#' . $map_params['table_id'] . '_checkall_count_bottom").html(count + \' items selected\');
			    });
			});
			';

			$html .= '</script>';
		}

		if (isset($inline_js) && $inline_js) {
			$html .= '<script type="text/javascript" charset="utf-8">';

			$html .= '
				tblmap_inline_toggle = function(id) {
					$("#" + id + "_inline").toggle();
					$("#" + id + "_inline_edit").toggle();
				}

				tblmap_inline_validate = function(id, validate) {
					var valid = true;
					if(validate == "1") {
						if($("#" + id + "_inline_element").validationEngine("validate")) {
							valid = false;
						} else {
							valid = true;
						}
					}
					return valid;
				}

				tblmap_inline_save = function(id, name, model, url, params) {
					var data = "model=" + model + "&params[" + name + "]=" + encodeURIComponent($("#" + id + "_inline_element").val()) + "&" + params;
					$.ajax({
						url: url,
						type: "POST",
						data: data,
						async: false,
						success: function() {
							tblmap_inline_toggle(id);
							$("#" + id + "_inline_text").html($("#" + id + "_inline_element").val());
						}
					});
				}
			';

			$html .= '</script>';
		}

		$html .= '<script>$(document).ready(function() {	$(".cmd-tip").tooltip(); });</script>';
		return $html;
	}

	public static function trimtext($string, $max_length = 20, $strict = true) {
		if (strlen($string) > $max_length) {
			$offset = ($max_length - 3) - strlen($string);
			if ($strict) {
				$string = substr($string, 0, $offset) . '...';
			} else {
				$string = substr($string, 0, strrpos($string, '', $offset)) . '...';
			}
		}
		return $string;
	}

	/*
		 * @Param : String
		 * Decodes the json string and returns the values for display
		 *
	*/
	public static function decode($json) {
		$decodevalue = "";
		$jsob_array = json_decode($json);
		foreach ($jsob_array as $key => $value) {
			if (!is_object($value)) {
				if (is_array($value)) {
					foreach ($value as $k => $v) {
						$decodevalue .= preg_replace("/(\)\))+/", "", preg_replace("/(params:stdClass::__set_state\(array\()+/", "", (($k == '0') ? $key : "") . ":" . var_export($v, true) . "\n"));
					}
				} else {
					if ($key != '0clone0') {
						$decodevalue .= (($key == '0') ? "" : $key) . ": " . $value . "\n";
					}
				}
			} else {
				$decodevalue .= ($key == 'params') ? "" : $key;
				$decodevalue .= self::decode(json_encode($value));
			}
		}
		return preg_replace("/(<br\s*\/?>\s*)+/", "<br/>", nl2br($decodevalue));
	}
}
?>