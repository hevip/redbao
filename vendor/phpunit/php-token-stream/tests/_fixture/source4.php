<?php
// Declare the interfaces 'iTemplate'
interface iTemplate
{
    public function setVariable($name, $var);
    public function
        getHtml($template);
}

interface a
{
    public function foo();
}

interface b extends a
{
    public function baz(Baz $baz);
}

// short desc for class that implement a unique interfaces
class c implements b
{
    public function foo()
    {
    }

    public function baz(Baz $baz)
    {
    }
}
