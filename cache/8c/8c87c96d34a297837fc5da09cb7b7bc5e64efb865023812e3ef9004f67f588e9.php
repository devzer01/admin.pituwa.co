<?php

/* profile.twig.html */
class __TwigTemplate_4a6d778ec07c35c4959b09fab0f4a8b697e921b1ddc32a72234677940d421bae extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 1
        $this->parent = $this->loadTemplate("layout.twig.html", "profile.twig.html", 1);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "layout.twig.html";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "<h1>User List</h1>
<ul>
    <li><a href=\"";
        // line 6
        echo twig_escape_filter($this->env, $this->env->getExtension('slim')->pathFor("profile", array("name" => "josh")), "html", null, true);
        echo "\">Josh</a></li>
</ul>
";
    }

    public function getTemplateName()
    {
        return "profile.twig.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  35 => 6,  31 => 4,  28 => 3,  11 => 1,);
    }
}
/* {% extends "layout.twig.html" %}*/
/* */
/* {% block content %}*/
/* <h1>User List</h1>*/
/* <ul>*/
/*     <li><a href="{{ path_for('profile', { 'name': 'josh' }) }}">Josh</a></li>*/
/* </ul>*/
/* {% endblock %}*/
