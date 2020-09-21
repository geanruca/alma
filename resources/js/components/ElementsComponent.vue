<template>
  <div class="container-fluid">
  <!-- Modal -->
  <div class="modal fade" id="create_element" tabindex="-1" role="dialog" aria-labelledby="create_element_tittle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Agregar una nueva tarea</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <span class="input-group-text">Nombre del elemento</span>
            </div>
            <input v-model="new_name" type="text" class="form-control" max-length='100' placeholder="nombre" aria-label="nombre" aria-describedby="basic-addon1">
          </div>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <label class="input-group-text" for="inputGroupSelect01">Padre</label>
            </div>
            <select v-model="new_parent" @change='parent_colour()' class="custom-select" id='gerar_select'  >
              <option default value="0">Sin padre</option>
              <option v-for="a in elements" :key="a.id" :value="a.id" >{{a.name}}</option>
            </select>
          </div>

          <div class="input-group mb-3">
            <div class="input-group-prepend">
              <label class="input-group-text" for="inputGroupSelect01">Color</label>
            </div>
            <select v-model='new_colour' class="custom-select"  id='gerar_colour' >
              <option value="#6DCFF6">Celeste</option>
              <option value="#BC8CBE">Morado</option>
              <option value="#ABD373">Verde</option>
              <option value="#FFF79A">Amarillo</option>
              <option value="#F49AC0">Rosado</option>
              <option value="#F7941D">Naranjo</option>
            </select>
          </div>
         

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="button" @click='create_element()' data-dismiss="modal" class="btn btn-primary">Agregar elemento</button>
        </div>
      </div>
    </div>
  </div>
  <!-- fin del modal -->
    <div class="row">
      <div class="col-12 d-inline-block mb-1">
        <button data-toggle="modal" data-target="#create_element" class="btn btn-sm btn-dark rounded float-right">Crear nuevo elemento</button>
      </div>
    </div>
    <div v-if="elements">
      <div v-for="index in last_sequence" :key="index">
        <div class="d-inline-block" v-for='el in elements' :key="el.id">
          <div  v-if="index == el.sequence" class="d-inline-block circulito wrapper" :style="'background-color:' + el.colour" >
            <span class="circulito" >
              {{el.name}}
            </span>
            <div class="tooltip">
              {{el.name}}
            </div>
            <div class="circulito2">
              <button  @click='eliminar(el.id)' class="btn btn-sm text-light p-0 m-0">
                <h2 class="p-0 m-0" >-</h2>
              </button>
            </div>
          </div>
        </div>
        <!-- <div style="background-color: #6DCFF6;" class="d-inline-block circulito"><span class="circulito">Celeste</span></div>
        <div style="background-color: #BC8CBE;" class="d-inline-block circulito"><span class="circulito">Morado</span></div>
        <div style="background-color: #ABD373;" class="d-inline-block circulito"><span class="circulito">Verde</span></div>
        <div style="background-color: #FFF79A;" class="d-inline-block circulito"><span class="circulito">Amarillo</span></div>
        <div style="background-color: #F49AC0;" class="d-inline-block circulito"><span class="circulito">Rosado</span></div>
        <div style="background-color: #F7941D;" class="d-inline-block circulito"><span class="circulito">Naranjo</span></div> -->
        <hr>
      </div>

    </div>
  </div>

</template>


<script>
import miniToastr from 'mini-toastr';
miniToastr.init();
  export default {
    data() {
      return {
        elements  : null,
        new_name  : null,
        new_colour: '#6DCFF6',
        new_parent: 0, 
      }
    },
    mounted(){
      miniToastr.success('Componente montado con éxito', 'Buen día!')
      this.get_elements();
    },
    filters: {
      filter_capitalize:    function(value) {
        return value.toLowerCase().split(' ').map( (i, j) => i.charAt(0).toUpperCase()+i.slice(1)).join(' ');
      },
      filtroDocumento:      function (value) {
          value = value.split('/')
          value = value[value.length-1]
          return value;
      },
      filtroSacarComillas:  function(value) {
          value = value.split('"');
          value = value[1];
          return value;
        
      },
      minusculas:           function(palabra){
        if(palabra != null){

          return palabra.toLowerCase()
        }
      },
      arreglarFecha:        function(fecha){
        let fecha_final =  Date.parse(fecha)
        fecha_final = new Date(fecha_final)
        if(fecha_final == null){
          return null;
        }
        let seg = fecha_final.getSeconds()
        let min = fecha_final.getMinutes()
        let hour = fecha_final.getHours()
        if(seg < 10){
            seg = '0'+seg;
        }
        if(min < 10){
            min = '0'+min;
        }
        if(hour < 10){
            hour = '0'+hour;
        }
        return fecha_final.getMonth() + '/' +fecha_final.getDate() + '/'+fecha_final.getFullYear() + ' ' + hour  + ':' + min  + ':' + seg;
      },
    },
    methods: {
      create_element(){
        let formData = new FormData();
            formData.append('name',this.new_name);
            formData.append('colour',this.new_colour);
            formData.append('parent',this.new_parent);
        axios.post('api/elements', formData)
        .then(response=>{
            miniToastr.success(response.msg, 'Elemento ingresado')
            this.get_elements()
        })
        .catch(error => {
          miniToastr.error(error, 'error')
        })

      },
      eliminar(id){
        var formData2 = new FormData();
            formData2.append('id',id);
            formData2.append('_method','DELETE');
            axios.post('api/elements/'+id, formData2)
            .then(response=>{
              this.get_elements()
              miniToastr.error('Ok.', 'Elemento eliminado')
            })
        .catch(error => {
          miniToastr.error(error, 'Error de server')
        })

      },
      get_elements(){
        axios.get('/api/elements')
        .then(response => {
          this.elements      = response.data.elements
          this.last_sequence = response.data.last_sequence
          this.new_colour    = response.data.default_colour
          console.log(response.data)
        })
        .catch(error => {
          
        })
      },
      parent_colour(){
        let e            = document.getElementById("gerar_select");
        let selected = e.options[e.selectedIndex].value;
        let gerar_colour = document.getElementById("gerar_colour");
        let new_parent   = this.new_parent
        let elements     = this.elements
        if(this.new_parent == '0'){
          return gerar_colour.removeAttribute('disabled');
        }
        let colour       = this.elements.find(x => x.id == selected).colour
        this.new_colour  = colour;
        gerar_colour.setAttribute('disabled', 'true');
        return gerar_colour.setAttribute('disabled', 'true');

    },
    },
  }
  
</script>
<style>
span.circulito{
position: absolute;
left:0;
right:0;
margin-left:auto;
margin-right:auto;
top: 0;
margin-top: 35%;
word-break: break-all;
white-space: nowrap;
overflow: hidden;
text-overflow: ellipsis;
}
div.circulito{
border: 1px solid #7b7676;
border-radius: 50%;
width: 100px;
position: relative;
height: 100px;
margin: 10px;
text-align: center;
word-break: break-all;
}
div.circulito2 {
position: absolute;
border: 1px solid #D3455B;
background: #D3455B;
color: white;
border-radius: 50%;
height: 30px;
width: 30px;
font-size: 1rem;
font-weight: bold;
top: 70%;
right: 0;
}
button > h2{
  line-height: 1;
}
.wrapper {
text-transform: uppercase;
background: #ececec;
color: #555;
font-family: "Gill Sans", Impact, sans-serif;
font-size: 20px;
margin: 100px 75px 10px 75px;
padding: 15px 20px;
position: relative;
text-align: center;
-webkit-transform: translateZ(0); /* webkit flicker fix */
-webkit-font-smoothing: antialiased; /* webkit text rendering fix */
}

.wrapper .tooltip {
  
background: #1496bb;
top: 100%;
color: #fff;
display: block;
left: 0px;
margin-top: 5px;
margin-bottom: 5px;
opacity: 0;
padding: 5px;
pointer-events: none;
position: absolute;
min-width: 200px;
-webkit-transform: translateY(10px);
  -moz-transform: translateY(10px);
  -ms-transform: translateY(10px);
   -o-transform: translateY(10px);
    transform: translateY(10px);
-webkit-transition: all .25s ease-out;
  -moz-transition: all .25s ease-out;
  -ms-transition: all .25s ease-out;
   -o-transition: all .25s ease-out;
    transition: all .25s ease-out;
-webkit-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
  -moz-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
  -ms-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
   -o-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
    box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
}

/* This bridges the gap so you can mouse into the tooltip without it disappearing */
/* .wrapper .tooltip:before {
bottom: -20px;
content: " ";
display: block;
height: 200px;
left: 0;
position: absolute;
width: 100%;
} */

.wrapper:hover .tooltip {
opacity: 1;
pointer-events: auto;
-webkit-transform: translateY(0px);
  -moz-transform: translateY(0px);
  -ms-transform: translateY(0px);
   -o-transform: translateY(0px);
    transform: translateY(0px);
}

/* IE can just show/hide with no transition */
.lte8 .wrapper .tooltip {
display: none;
}

.lte8 .wrapper:hover .tooltip {
display: block;
}
</style>
