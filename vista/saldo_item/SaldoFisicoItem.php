<?php
/**
 *@package pXP
 *@file gen-Planilla.php
 *@author  (admin)
 *@date 22-01-2014 16:11:04
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.SaldoFisicoItem=Ext.extend(Phx.gridInterfaz,{

        bnew: false,
        bedit: false,
        bdel: false,
        btest: false,
        bsave: false,

        constructor:function(config){
            this.tbarItems = ['-','Gestión:',
                this.cmbGestion,'-'

            ];
            this.maestro=config.maestro;
            //llama al constructor de la clase padre
            Phx.vista.SaldoFisicoItem.superclass.constructor.call(this,config);
            this.init();

            //this.store.baseParams.pes_estado = 'consultas';

            var fecha = new Date();
            Ext.Ajax.request({
                url:'../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                params:{fecha:fecha.getDate()+'/'+(fecha.getMonth()+1)+'/'+fecha.getFullYear()},
                success:function(resp){
                    var reg =  Ext.decode(Ext.util.Format.trim(resp.responseText));
                    this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                    this.cmbGestion.setRawValue(reg.ROOT.datos.anho);
                    this.store.baseParams.id_gestion=reg.ROOT.datos.id_gestion;
                    this.load({params:{start:0, limit:this.tam_pag}});
                },
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });

            this.cmbGestion.on('select',this.capturarEventos, this);
            //this.store.baseParams.tipo_interfaz =  this.nombreVista;

            this.iniciarEventos();

            this.addButton('btn_refresh',
                {	grupo:[0,1,2],
                    text: 'Actualizar Saldo',
                    iconCls: 'bactfil',
                    disabled: false,
                    handler: this.actualizarSaldo,
                    tooltip: '<b>Actualizar Saldo</b><br/>Actualiza la tabla tsaldo_fisico_item con los saldos finales de una Gestión'
                }
            );
        },
        actualizarSaldo : function() {
            Phx.CP.loadWindows('../../../sis_almacenes/vista/saldo_item/FechaCierreGestion.php',
                'Cierre',
                {
                    modal:true,
                    width:350,
                    height:150
                },this.maestro,this.idContenedor,'FechaCierreGestion')
        },

        cmbGestion : new Ext.form.ComboBox({
            name: 'gestion',
            id: 'gestion_upd',
            fieldLabel: 'Gestión',
            allowBlank: true,
            emptyText:'Gestion...',
            blankText: 'Año',
            store:new Ext.data.JsonStore(
                {
                    url: '../../sis_parametros/control/Gestion/listarGestion',
                    id: 'id_gestion',
                    root: 'datos',
                    sortInfo:{
                        field: 'gestion',
                        direction: 'DESC'
                    },
                    totalProperty: 'total',
                    fields: ['id_gestion','gestion'],
                    // turn on remote sorting
                    remoteSort: true,
                    baseParams:{par_filtro:'gestion'}
                }),
            valueField: 'id_gestion',
            triggerAction: 'all',
            displayField: 'gestion',
            hiddenName: 'id_gestion',
            mode:'remote',
            pageSize:50,
            queryDelay:500,
            listWidth:'280',
            hidden:false,
            width:80
        }),
        capturarEventos: function () {
            this.store.baseParams.id_gestion=this.cmbGestion.getValue();
            this.load({params:{start:0, limit:this.tam_pag}});
        },
        Atributos:[
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
                    name: 'codigo_item',
                    fieldLabel: 'Codigo Item',
                    allowBlank: false,
                    //anchor: '100%',
                    gwidth: 200,
                    maxLength:100
                },
                type:'TextField',
                filters:{pfiltro:'item.codigo',type:'string'},
                id_grupo:1,
                grid:true,
                form:false,
                bottom_filter : true
            },

            {
                config:{
                    name: 'nombre_item',
                    fieldLabel: 'Nombre Item',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 300
                },
                type:'TextField',
                filters:{pfiltro:'item.nombre',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    name: 'saldo',
                    fieldLabel: 'Saldo Fisico',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 300
                },
                type:'TextField',
                filters:{pfiltro:'sal.fisico',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    name: 'fecha',
                    fieldLabel: 'Fecha Cierre',
                    allowBlank: false,
                    qtip: 'Esta fecha se tomara como base para afectaciones contables y presupuestarias de esta planilla',
                    anchor: '80%',
                    gwidth: 100,
                    format: 'd/m/Y',
                    renderer:function (value,p,record){return value?value.dateFormat('d/m/Y'):''}
                },
                type:'DateField',
                filters:{pfiltro:'sal.fecha_hasta',type:'date'},
                id_grupo:1,
                grid:true,
                form:true
            },

            {
                config:{
                    name: 'nombre_alm',
                    fieldLabel: 'Almacen',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 300
                },
                type:'TextField',
                filters:{pfiltro:'alm.nombre',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            },

            {
                config:{
                    name: 'gestion',
                    fieldLabel: 'Gestion',
                    allowBlank: true,
                    anchor: '80%',
                    gwidth: 300
                },
                type:'TextField',
                filters:{pfiltro:'ges.gestion',type:'string'},
                id_grupo:1,
                grid:true,
                form:false
            }


        ],
        tam_pag:50,
        title:'SaldoFisicoItem',
        ActList:'../../sis_almacenes/control/Item/listarSaldoFisicoItem',
        id_store:'id_item',
        fields: [
            {name:'id_item', type: 'numeric'},
            {name:'codigo_item', type: 'string'},
            {name:'nombre_item', type: 'string'},
            {name:'id_almacen', type: 'numeric'},
            {name:'codigo_alm', type: 'string'},
            {name:'nombre_alm', type: 'string'},
            {name:'id_gestion', type: 'numeric'},
            {name:'gestion', type: 'numeric'},
            {name:'fecha', type: 'date',dateFormat:'Y-m-d'},
            {name:'saldo', type: 'numeric'}

        ],
        sortInfo:{
            field: 'id_item',
            direction: 'asc'
        }
        /*,
        iniciarEventos : function() {
            this.Cmp.id_gestion.on('select',function(c,r,i){
                this.Cmp.id_periodo.reset();
                this.Cmp.id_periodo.store.baseParams.id_gestion = r.data.id_gestion;
            },this);


        },
        preparaMenu:function()
        {	var rec = this.sm.getSelected();
            //this.desactivarMenu();
            Phx.vista.SaldoFisicoItem.superclass.preparaMenu.call(this);


            this.getBoton('btnObs').enable();
            this.getBoton('btnChequeoDocumentosWf').enable();

            //MANEJO DEL BOTON DE GESTION DE PRESUPUESTOS
            this.getBoton('diagrama_gantt').enable();

        },
        liberaMenu:function()
        {
            this.desactivarMenu();
            Phx.vista.SaldoFisicoItem.superclass.liberaMenu.call(this);

        }*/

    });
</script>