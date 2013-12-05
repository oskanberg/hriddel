<?php

class ColumnsView extends View
{
    private $template = 'columns.tpl';
    private $title = 'Columns';
    
    public function display()
    {
        $data = array(
            'title' => $this->title,
            'view_specific_template' => $this->template,
            'column_articles' => $this->_model->get_recent_column_articles(500)
        );
        $data['column_names'] = array();
        foreach ($data['column_articles'] as $column)
        {
            if (!in_array($column->column_name, $data['column_names']))
            {
                $data['column_names'][] = $column->column_name;
            }
        }
        include_once(TEMPLATE_PATH . 'base_template.tpl');
    }
}

?>