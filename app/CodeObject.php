<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CodeObject extends Model
{
    public $guarded = [];


    public function getHtml() {
        return '<div class="d-flex justify-content-end"><span class="badge badge-light">'. $this->language .'</span></div><pre><code class="language-' . $this->language . '">' . $this->code . '</code></pre>';
        //return '<prism-editor language="'. $this->language .'" code="' . $this->code . '" line-numbers"></prism-editor>';

        //return '<component is="table-loading"></component>';
    }
}
