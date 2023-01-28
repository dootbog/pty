<?php

declare(strict_types=1);

namespace Pty;


function colorize(string $text, ...$codes): string
{
    $f = fn (int $n=null) => is_null($n) ? '' : sprintf("\x1b[%sm", str_pad((string)$n, 3, '0', STR_PAD_LEFT));
    $x = '';
    foreach ($codes as $code) {
        $x .= $f($code);
    }
    return $x . $text . $f(0);
}

function in() { return fgets(STDIN); }
function out(...$inputs) { foreach ($inputs as $out) fwrite(STDOUT, $out); }
function width() { return (int)`tput cols`; }
function height() { return (int)`tput lines`; }
function off() { out('\u001b[0m'); }
function clear() { out("\033[2J"); }
function eol() { out("\033[K"); }
function save() { out( "\033[s"); }
function restore() { out( "\033[u"); }
function to(int $x, int $y) { out("\033[{$y};{$x}H"); }
function up(int $to) { out("\033[{$to}A"); }
function down(int $to) { out("\033[{$to}B"); }
function right(int $to) { out("\033[{$to}C"); }
function left(int $to) { out("\033[{$to}D"); }

function box(string $txt, int $x, int $y, int $w, int $h): void
{
    save();
    for ($col = 0; $col < $h; $col++) {
        for ($row = 0; $row < $w; $row++) {
            to($x + $row, $y + $col);
            out($txt);
        }
    }
    restore();
}

function frame(string $txt, int $x, int $y, int $w, int $h): void
{
    save();
    for ($i = 0; $i < $w; $i++) {
        to($x + $i, $y);
        out($txt);
        to($x + $i, $y + $h);
        out($txt);
    }
    for ($i = 0; $i <= $h; $i++) {
        to($x, $y + $i);
        out($txt);
        to($x + $w, $y + $i);
        out($txt);
    }
    restore();
}

final class Code
{
    public const OFF = 0;
    public const BOLD = 1;
    public const ITALIC = 3;
    public const UNDERLINE = 4;
    public const BLINK = 5;
    public const INVERSE = 7;
    public const HIDDEN = 8;
    public const BLACK = 30;
    public const RED = 31;
    public const GREEN = 32;
    public const YELLOW = 33;
    public const BLUE = 34;
    public const MAGENTA = 35;
    public const CYAN = 36;
    public const WHITE = 37;
    public const BRIGHTBLACK = 90;
    public const BRIGHTWHITE = 97;
    public const BLACKBG = 40;
    public const REDBG = 41;
    public const GREENBG = 42;
    public const YELLOWBG = 43;
    public const BLUEBG = 44;
    public const MAGENTABG = 45;
    public const CYANBG = 46;
    public const WHITEBG = 47;
}

final class Keyboard
{
    private $in;

    public function __construct()
    {
        readline_callback_handler_install('', fn () => null);
        $this->in = fopen('php://stdin', 'r');
    }

    public function __destruct()
    {
        readline_callback_handler_remove();
        fclose($this->in);
    }
    
    public function get()
    {
        $w = $e = null;
        $s = 0;
        $r = [$this->in];
        $delta = stream_select($r, $w, $e, $s);

        if ($delta > 0) {
            return stream_get_contents($this->in, 1);
        } else {
            return null;
        }
    }
}


