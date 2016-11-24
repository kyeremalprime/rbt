<?php

class RBTree {
    public $root;
    public $nil;
    
    public function __construct() {
        $this->nil = array("left" => null ,"right" => null,"parent" => null,"color" => "BLACK","isnil" => true,"data" => "sentinel");
        $this->root = &$this->nil;
    }
    
    public function Isnil(&$n) {
        return $n["isnil"];
    }

    public function insert($n) {
        $y = &$this->nil;
        $x = &$this->root;
        
        while( !$this->Isnil($x) ) {
            $y = &$x;
            if ($n["data"] < $x["data"]) $x = &$x["left"];
            else $x = &$x["right"];
        }
        
        if( $this->Isnil($y)) $this->root = &$n;
        else if( $n["data"] < $y["data"] ) $y["left"] = &$n;
        else $y["right"] = &$n;
            
        $n["parent"] = &$y;    
        $n["left"] = &$this->nil;
        $n["right"] = &$this->nil;
        $n["color"] = "RED";
        $n["isnil"] = false;
        
        $this->insertFixup($n);
    }
    
    public function insertFixup(&$node) {    
        
        $n = &$node;
        while ( $n["parent"]["color"]  == "RED" ) {
            $tmp = &$n["parent"]["parent"];
            if( $n["parent"]["data"] == $tmp["left"]["data"] ) {
                $y = &$n["parent"]["parent"]["right"];
                if( $y["color"] == "RED" ) {
                    $n["parent"]["color"] = "BLACK";
                    $y["color"] = "BLACK";
                    $n["parent"]["parent"]["color"] = "RED";
                    $n = &$n["parent"]["parent"];
                }
                else {
                    if ( $n["data"] == $n["parent"]["right"]["data"] ) {
                        $n  = &$n["parent"];
                        $this->leftRotate($n);
                    }
                    $n["parent"]["color"] = "BLACK";
                    $n["parent"]["parent"]["color"] = "RED";
                    $this->rightRotate($n["parent"]["parent"]);                    
                }
            }
            else {
                $y = &$n["parent"]["parent"]["left"];
                if( $y["color"] == "RED" ) {
                    $n["parent"]["color"] = "BLACK";
                    $y["color"] = "BLACK";
                    $n["parent"]["parent"]["color"] = "RED";
                    $n = &$n["parent"]["parent"];
                }
                else {
                    if ( $n["data"] == $n["parent"]["left"]["data"] ) {
                        $n  = &$n["parent"];
                        $this->rightRotate($n);
                    }
                    $n["parent"]["color"] = "BLACK";
                    $n["parent"]["parent"]["color"] = "RED";
                    $this->leftRotate($n["parent"]["parent"]);                    
                }            
            }
        }
        
        $this->root["color"] = "BLACK";
    }

    public function leftRotate(&$n)
    {
        $y = &$n["right"];
        $n["right"] = &$y["left"];
        
        if ( !$this->Isnil($y["left"]) ) {
            $y["left"]["parent"] = &$n;
        }
        
        $y["parent"] = &$n["parent"];
        
        if ( $this->Isnil($n["parent"]) ) {
            $this->root = &$y;
        }
        else if ( $n["data"] == $n["parent"]["left"]["data"] ) {
            $n["parent"]["left"] = &$y;
        }
        else {
            $n["parent"]["right"] = &$y;
        }
        
        $y["left"] = &$n;
        $n["parent"] = &$y;
    }

    public function rightRotate(&$n) {
        $y = &$n["left"];
        $n["left"] = &$y["right"];
        
        if ( !$this->Isnil($y["right"]) ) {
            $y["right"]["parent"] = &$n;
        }
        
        $y["parent"] = &$n["parent"];
        
        if ( $this->Isnil($n["parent"]) ) {
            $this->root = &$y;
        }
        else if ( $n["data"] == $n["parent"]["left"]["data"] ) {
            $n["parent"]["left"] = &$y;
        }
        else {
            $n["parent"]["right"] = &$y;
        }
        
        $y["right"] = &$n;
        $n["parent"] = &$y;
    }
    
    public function delete($data,&$r) {
        if ( $r == null ) return;
        if ( $data < $r["data"] ) $this->delete( $data, $r["left"] );
        else if ( $data > $r["data"] ) $this->delete( $data, $r["right"] );
        else if ( $r["left"] != null && $r["right"] != null ) {
            $min =  $this->findMin( $r["right"] );
            $r["data"] = $min["data"];
            $this->delete( $r["data"] , $r["right"]);
        }
        else {
            $r = ( $r["left"] != null ) ? $r["left"] : $r["right"];
        }
    }
    
    public function checkerror() {
        if($this->root["color"] == "RED") {
            return;
        }
    }
    
    public function transplant(&$u,&$v) {
        if ( $this->Isnil($u["parent"]) ) $this->root = &$v;
        else if ( $u["data"] == $u["parent"]["left"]["data"] ) $u["parent"]["left"] = &$v;
        else $u["parent"]["right"] = &$v;

        $v["parent"] = &$u["parent"];
    }
    
    public function delete2($data,&$r) {
        if ( $this->Isnil($r) ) return;
        if ( $data < $r["data"] ) $this->delete2( $data, $r["left"] );
        else if ( $data > $r["data"] ) $this->delete2( $data, $r["right"] );
        else {
            $y = &$r;
            $y_origin_color = $r["color"];
            
            if ( $this->Isnil($r["left"]) ) {
                $x = &$r["right"];
                $this->transplant($r,$r["right"]);
            }
            else if( $this->Isnil($r["right"]) ) {
                $x = &$r["left"];
                $this->transplant($r,$r["left"]);
            }
            else {
                $y =  &$this->findMin( $r["right"] );
                $y_origin_color = $y["color"];
                echo "y.data is ".$y["data"]." ".$r["data"]."n";
                
                $x = &$y["right"];
                
                if ( $y["parent"]["data"] == $r["data"])  {
                    $x["parent"] = &$y;
                }
                else {
                    $this->transplant($y,$y["right"]);
                    
                    $y["right"]  = &$r["right"];
                    $y["right"]["parent"] = &$y;
                }
                
                $this->transplant($r,$y);
                $y["left"] = &$r["left"];
                $y["left"]["parent"] = &$y;
                $y["color"] = $r["color"];
            }
        }
        
        if ( $y_origin_color == "BLACK" ) $this->deleteFixup($x);
    }
    
    public function deleteFixup(&$x) {
        while ( $x["data"] != $this->root["data"] && $x["color"] == "BLACK" ) {
            if ( $x["data"] == $x["parent"]["left"]["data"] ) {
                $s = &$x["parent"]["right"];
                if (  $s["color"] == "RED" ) {
                    $s["color"] = "BLACK";
                    $x["parent"]["color"] = "RED";
                    $this->leftRotate($x["parent"]);
                    $s = $x["parent"]["right"];
                }
                
                if ( $s["left"]["color"] == "BLACK" && $s["right"]["color"] == "BLACK" ) {
                    $s["color"] = "RED";
                    $x = &$x["parent"];
                }
                else {
                    if( $s["right"]["color"] == "BLACK" ) {
                        $s["left"]["color"] = "BLACK";
                        $s["color"] = "RED";
                        $this->rightRotate($s);
                        $s = &$x["parent"]["right"];
                    }
                    
                    $s["color"] = $x["parent"]["color"];
                    $x["parent"]["color"] = "BLACK";
                    $s["right"]["color"] = "BLACK";
                    $this->leftRotate($x["parent"]);
                    $x = &$this->root;
                }
            }
            else {
                $s = &$x["parent"]["left"];
                if (  $s["color"] == "RED" ) {
                    $s["color"] = "BLACK";
                    $x["parent"]["color"] = "RED";
                    $this->rightRotate($x["parent"]);
                    $s = $x["parent"]["left"];
                }
                
                if ( $s["right"]["color"] == "BLACK" && $s["left"]["color"] == "BLACK" ) {
                    $s["color"] = "RED";
                    $x = &$x["parent"];
                }
                else {
                    if( $s["left"]["color"] == "BLACK" ) {
                        $s["right"]["color"] = "BLACK";
                        $s["color"] = "RED";
                        $this->leftRotate($s);
                        $s = &$x["parent"]["left"];
                    }
                    
                    $s["color"] = $x["parent"]["color"];
                    $x["parent"]["color"] = "BLACK";
                    $s["left"]["color"] = "BLACK";
                    $this->rightRotate($x["parent"]);
                    $x = &$this->root;
                }
            }
        }
        
        $x["color"] = "BLACK";
    }
    
    public function & findMin( &$r ) {
        if ( $this->Isnil($r) ) return null;
        if ( $this->Isnil($r["left"]) ) return $r;
        return $this->findMin($r["left"]);
    }
    
    public function printTree() {
        $roots = array();
        $roots[] = $this->root; 
        $this->printTreeRecursion($roots);
    }
    
    public function printTreeRecursion($roots) {
        $nextroots = array();
        if( count($roots) == 0 ) return;
        
        for( $i = 0 ; $i < count($roots); $i++ ) {
            if( $roots[$i] != null) {
                echo $roots[$i]["data"]." ";
                $nextroots[] = $roots[$i]["left"];
                $nextroots[] = $roots[$i]["right"];
            }            
        }
        echo "n";
        
        $this->printTreeRecursion($nextroots);
    }
    
    public function printTreePreorder(&$r,$d) {
        for( $i = 0 ; $i < $d * 2 ; $i++ ) echo " ";
        
        if( $this->Isnil($r)) echo "nilln";
        else echo $r["data"]."(".$r["color"].") PARENT:".$r["parent"]["data"]."n";
        
        if( $this->Isnil($r)) return;
        $this->printTreePreorder($r["left"],$d+1);
        $this->printTreePreorder($r["right"],$d+1);
    }
    
    public function printTreeInorder(&$r,$d) {
        if ( $r != null ) $this->printTreeInorder($r["left"],$d+1);
        for( $i = 0 ; $i < $d * 2 ; $i++ ) echo " ";
        
        if( $r == null) echo "nilln";
        else echo $r["data"]."n";
        
        if( $r != null) $this->printTreeInorder($r["right"],$d+1);
    }

}

$rbt = new RBTree();

?>
