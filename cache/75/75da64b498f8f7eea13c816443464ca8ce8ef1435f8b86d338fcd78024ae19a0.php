<?php

/* layout.twig.html */
class __TwigTemplate_5bc9f3f2ab8e094683fa68e12231655c19c2ae6d6a03a1dda0d98eedad1cd1d7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
<head>
    ";
        // line 4
        $this->displayBlock('head', $context, $blocks);
        // line 8
        echo "</head>
<body>
<div id=\"content\">";
        // line 10
        $this->displayBlock('content', $context, $blocks);
        echo "</div>
<div id=\"footer\">
    ";
        // line 12
        $this->displayBlock('footer', $context, $blocks);
        // line 15
        echo "</div>
</body>
</html>";
    }

    // line 4
    public function block_head($context, array $blocks = array())
    {
        // line 5
        echo "    <link rel=\"stylesheet\" href=\"style.css\" />
    <title>";
        // line 6
        $this->displayBlock('title', $context, $blocks);
        echo " - My Webpage</title>
    ";
    }

    public function block_title($context, array $blocks = array())
    {
    }

    // line 10
    public function block_content($context, array $blocks = array())
    {
    }

    // line 12
    public function block_footer($context, array $blocks = array())
    {
        // line 13
        echo "    &copy; Copyright 2011 by <a href=\"http://domain.invalid/\">you</a>.
    ";
    }

    public function getTemplateName()
    {
        return "layout.twig.html";
    }

    public function getDebugInfo()
    {
        return array (  71 => 13,  68 => 12,  63 => 10,  53 => 6,  50 => 5,  47 => 4,  41 => 15,  39 => 12,  34 => 10,  30 => 8,  28 => 4,  23 => 1,);
    }
}
/* <!DOCTYPE html>*/
/* <html>*/
/* <head>*/
/*     {% block head %}*/
/*     <link rel="stylesheet" href="style.css" />*/
/*     <title>{% block title %}{% endblock %} - My Webpage</title>*/
/*     {% endblock %}*/
/* </head>*/
/* <body>*/
/* <div id="content">{% block content %}{% endblock %}</div>*/
/* <div id="footer">*/
/*     {% block footer %}*/
/*     &copy; Copyright 2011 by <a href="http://domain.invalid/">you</a>.*/
/*     {% endblock %}*/
/* </div>*/
/* </body>*/
/* </html>*/
