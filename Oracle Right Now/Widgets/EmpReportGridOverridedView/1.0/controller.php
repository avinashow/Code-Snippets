<?php
namespace Custom\Widgets\input\USTrainee;

class EmpReportGridOverridedView extends \RightNow\Widgets\Grid {
    function __construct($attrs) {
        parent::__construct($attrs);
    }

    function getData() {
        //return parent::getData();
		$this->data['attrs']['label_row_number'] = "Serial Number";
		$format = array(
            'truncate_size' => $this->data['attrs']['truncate_size'],
            'max_wordbreak_trunc' => $this->data['attrs']['max_wordbreak_trunc'],
            'emphasisHighlight' => $this->data['attrs']['highlight'],
            'recordKeywordSearch' => true,
            'dateFormat' => $this->data['attrs']['date_format'],
            //'urlParms' => \RightNow\Utils\Url::getParametersFromList($this->data['attrs']['add_params_to_url']),
            'hiddenColumns' => true,
        );

        $filters = array('recordKeywordSearch' => true);
        \RightNow\Utils\Url::setFiltersFromAttributesAndUrl($this->data['attrs'], $filters);
        $reportToken = \RightNow\Utils\Framework::createToken($this->data['attrs']['report_id']);
        $results = $this->CI->model('Report')->getDataHTML($this->data['attrs']['report_id'], $reportToken, $filters, $format)->result;
        if ($results['error'] !== null)
        {
            echo $this->reportError($results['error']);
            return false;
        }
        $this->data['tableData'] = $results;
		$headers = $this->data['tableData']['headers'];
		
		$template = '<table id="<%= tableID %>" class="yui3-datatable-table" role="grid"> <caption><%= caption %></caption> <thead class="yui3-datatable-columns">  <tr>  <% for (var h=0; h < headers.length - 1; h++) { %>  <th class="yui3-datatable-header yui3-datatable-sortable-column" style="<%= headers[h].style %>"><%= headers[h].label %></th> <% } %> </tr> </thead> <tbody class="yui3-datatable-data">  <% for (var r=0; r < rows.length; r++) { %>  <tr role="row" class="yui3-datatable-<%= (r % 2 === 0) ? "even" : "odd" %>"> <% for (var d=1; d < rows[r].length - 7; d++) { %> <td role="gridcell" class="yui3-datatable-cell"> <% if(d == 5){ %> <% if(rows[r][9].includes(rows[r][d])){ %> <input type="checkbox" value="<%= rows[r][d] %>" checked> <% }else { %> <input type="checkbox" value="<%= rows[r][d] %>">  <%} } else if(d == 6){ %> <% if(rows[r][9].includes(rows[r][d])){ %> <input type="checkbox" value="<%= rows[r][d] %>" checked> <% }else { %> <input type="checkbox" value="<%= rows[r][d] %>"> <%} } else{ %> <%= rows[r][d] %> <% } %> </td> <% } %> </tr> <% } %> </tbody> </table>';
		
		$filters['page'] = $results['page'];
        $this->data['js'] = array(
            'filters'       => $filters,
            'columnID'      => (int) $filters['sort_args']['filters']['col_id'],
            'sortDirection' => (int) $filters['sort_args']['filters']['sort_direction'],
            'format'        => $format,
            'token'         => $reportToken,
            'headers'       => $this->data['tableData']['headers'],
            'rowNumber'     => $this->data['tableData']['row_num'],
            'searchName'    => 'sort_args',
            'dataTypes'     => array('date' => VDT_DATE, 'datetime' => VDT_DATETIME, 'number' => VDT_INT),
			'result'		=> $this->data['tableData']['data'],
			'dataTable'		=> $template,
			'dataArrObj'	=> $dataArrayObj
        );
    }
}