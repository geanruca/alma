<?php

namespace App\Http\Controllers\API;

use SweetAlert;
use App\Element;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ElementsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $elements = Element::all();
        $colours = Element::select('colour')->distinct()->get()->pluck('colour')->toArray();
        // dd($colours);
        $default_colours = [
            '#6DCFF6',
            '#BC8CBE',
            '#ABD373',
            '#FFF79A',
            '#F49AC0',
            '#F7941D'
        ];
        // $default_colour = array_diff($default_colours, $colours)[0] ?? '#6DCFF6';
        $default_colour = array_values(array_diff($default_colours, $colours))[0] ?? '#6DCFF6';
        // dd($default_colour);
        $last_sequence  = Element::orderBy('sequence', 'desc')->first();
        if($last_sequence){
            $last_sequence = $last_sequence->sequence;
        }else{
            $last_sequence = 1;
        }
        return response()->json(compact('elements', 'default_colour', 'last_sequence'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $r
     * @return \Illuminate\Http\Response
     */
    public function store(Request $r)
    {
        // $r->validate([

        // ]);
        if($r->parent == 0){
            $r->parent = null;
        }
        $last_id  = Element::orderBy('id', 'desc')->first();
        if($last_id){
            $last_id = $last_id->id ?? 1;
        }
        $parent = Element::find($r->parent);
        if($parent){
            $new_sequence = $parent->sequence + 1;
            $new_colour   = $parent->colour;
        }
        $element           = new Element;
        $element->name     = $r->name      ?? 'Element '.$last_id;
        $element->sequence = $new_sequence ?? 1;
        $element->colour   = $new_colour   ?? $r->colour;
        $element->parent   = $r->parent    ?? null;
        $element->save();
        return response()->json('Guardado!');


    }

   
    public function update(Request $r, $id)
    {
        $element = Element::find($id);
        if($r->parent == 0 OR $r->parent == $element->parent){
            $parent_value = null;
        }

        $parent = Element::find($r->parent);
        
        if($parent){
            $new_sequence = $parent->sequence + 1;
            $new_colour   = $parent->colour;
        }
        
        $element->name     = $r->name         ?? $element->name;
        $element->sequence = $new_sequence    ?? $element->sequence;
        $element->colour   = $new_colour      ?? '#6DCFF6';
        $element->parent   = $parent_value    ?? $element->parent;
        $element->save();

        return response()->json('Guardado!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $e = Element::where('id', $id)->delete();
        return response()->json('Eliminado!');
    }
}
