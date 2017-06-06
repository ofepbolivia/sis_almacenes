<?php
/**
*@package pXP
*@file gen-ItemConceptoIngas.php
*@author  (gsarmiento)
*@date 18-05-2017 14:01:28
*@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
*/

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
Phx.vista.ItemConceptoIngas=Ext.extend(Phx.gridInterfaz,{

	constructor:function(config){
		this.maestro=config.maestro;
    	//llama al constructor de la clase padre
		Phx.vista.ItemConceptoIngas.superclass.constructor.call(this,config);
		this.init();
		this.store.baseParams={id_item : this.id_item};

		this.load({params:{start:0, limit:this.tam_pag}})
		this.Atributos[1].valorInicial = this.id_item;

	},
			
	Atributos:[
		{
			//configuracion del componente
			config:{
					labelSeparator:'',
					inputType:'hidden',
					name: 'id_item_concepto_ingas'
			},
			type:'Field',
			form:true 
		},
		{
			//configuracion del componente
			config:{
				labelSeparator:'',
				inputType:'hidden',
				name: 'id_item'
			},
			type:'Field',
			form:true
		},
		{
			config:{
				name: 'id_concepto_ingas',
				fieldLabel: 'Concepto',
				allowBlank: false,
				emptyText : 'Concepto...',
				store : new Ext.data.JsonStore({
					url:'../../sis_parametros/control/ConceptoIngas/listarConceptoIngasMasPartida',
					id : 'id_concepto_ingas',
					root: 'datos',
					sortInfo:{
						field: 'desc_ingas',
						direction: 'ASC'
					},
					totalProperty: 'total',
					fields: ['id_concepto_ingas','tipo','desc_ingas','movimiento','desc_partida','id_grupo_ots','filtro_ot','requiere_ot'],
					remoteSort: true,
					baseParams:{par_filtro:'desc_ingas#par.codigo',movimiento:'gasto', autorizacion: 'adquisiciones'}
				}),
				valueField: 'id_concepto_ingas',
				displayField: 'desc_ingas',
				gdisplayField: 'desc_concepto_ingas',
				hiddenName: 'id_concepto_ingas',
				forceSelection:true,
				typeAhead: false,
				triggerAction: 'all',
				listWidth:500,
				resizable:true,
				lazyRender:true,
				mode:'remote',
				pageSize:10,
				queryDelay:1000,
				width:350,
				gwidth:200,
				minChars:2,
				qtip:'Si el conceto de gasto que necesita no existe por favor  comuniquese con el área de presupuestos para solictar la creación',
				tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>{desc_ingas}</b></p><strong>{tipo}</strong><p>PARTIDA: {desc_partida}</p></div></tpl>',
				renderer:function(value, p, record){return String.format('{0}', record.data['desc_ingas']);}
			},
			type:'ComboBox',
			filters:{pfiltro:'cgas.desc_ingas',type:'string'},
			id_grupo:1,
			grid:true,
			form:true
		},
		{
			config:{
				name: 'estado_reg',
				fieldLabel: 'Estado Reg.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:10
			},
				type:'TextField',
				filters:{pfiltro:'itmingas.estado_reg',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'id_usuario_ai',
				fieldLabel: '',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'itmingas.id_usuario_ai',type:'numeric'},
				id_grupo:1,
				grid:false,
				form:false
		},
		{
			config:{
				name: 'usr_reg',
				fieldLabel: 'Creado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu1.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usuario_ai',
				fieldLabel: 'Funcionaro AI',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:300
			},
				type:'TextField',
				filters:{pfiltro:'itmingas.usuario_ai',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_reg',
				fieldLabel: 'Fecha creación',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'itmingas.fecha_reg',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'fecha_mod',
				fieldLabel: 'Fecha Modif.',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
							format: 'd/m/Y', 
							renderer:function (value,p,record){return value?value.dateFormat('d/m/Y H:i:s'):''}
			},
				type:'DateField',
				filters:{pfiltro:'itmingas.fecha_mod',type:'date'},
				id_grupo:1,
				grid:true,
				form:false
		},
		{
			config:{
				name: 'usr_mod',
				fieldLabel: 'Modificado por',
				allowBlank: true,
				anchor: '80%',
				gwidth: 100,
				maxLength:4
			},
				type:'Field',
				filters:{pfiltro:'usu2.cuenta',type:'string'},
				id_grupo:1,
				grid:true,
				form:false
		}
	],
	tam_pag:50,	
	title:'Item Concepto Ingas',
	ActSave:'../../sis_almacenes/control/ItemConceptoIngas/insertarItemConceptoIngas',
	ActDel:'../../sis_almacenes/control/ItemConceptoIngas/eliminarItemConceptoIngas',
	ActList:'../../sis_almacenes/control/ItemConceptoIngas/listarItemConceptoIngas',
	id_store:'id_item_concepto_ingas',
	fields: [
		{name:'id_item_concepto_ingas', type: 'numeric'},
		{name:'id_item', type: 'numeric'},
		{name:'id_concepto_ingas', type: 'numeric'},
		{name:'desc_ingas', type: 'string'},
		{name:'estado_reg', type: 'string'},
		{name:'id_usuario_ai', type: 'numeric'},
		{name:'id_usuario_reg', type: 'numeric'},
		{name:'usuario_ai', type: 'string'},
		{name:'fecha_reg', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'fecha_mod', type: 'date',dateFormat:'Y-m-d H:i:s.u'},
		{name:'id_usuario_mod', type: 'numeric'},
		{name:'usr_reg', type: 'string'},
		{name:'usr_mod', type: 'string'},
		
	],
	sortInfo:{
		field: 'id_item_concepto_ingas',
		direction: 'ASC'
	},
	bdel:true,
	bsave:true
	}
)
</script>
		
		