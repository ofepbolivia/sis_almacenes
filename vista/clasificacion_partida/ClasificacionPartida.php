<?php
/**
 * @package pXP
 * @file    ClasificacionPartida.php
 * @author  maylee.perez
 * @date    24-11-2020
 * @description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */

header("content-type: text/javascript; charset=UTF-8");
?>
<script>
    Phx.vista.ClasificacionPartida = Ext.extend(Phx.gridInterfaz, {
            constructor: function (config) {

                this.maestro = config.maestro;
                this.initButtons=[this.cmbGestion];
                console.log('llegaconstructor', this)
                //this.getComponente('id_item').setValue(0);
                Phx.vista.ClasificacionPartida.superclass.constructor.call(this, config);
                this.init();

                //Filtro  gestion
                Ext.Ajax.request({
                    url: '../../sis_parametros/control/Gestion/obtenerGestionByFecha',
                    params: {fecha: new Date()},
                    success: function (resp) {
                        var reg = Ext.decode(Ext.util.Format.trim(resp.responseText));
                        this.cmbGestion.setValue(reg.ROOT.datos.id_gestion);
                        this.cmbGestion.setRawValue(reg.ROOT.datos.anho);
                        this.store.baseParams.id_gestion = reg.ROOT.datos.id_gestion;
                        this.load({params: {start: 0, limit: this.tam_pag}});
                    },
                    failure: this.conexionFailure,
                    timeout: this.timeout,
                    scope: this
                });


                this.cmbGestion.on('select', this.capturarEventos, this);
            },

            capturarEventos: function () {
                this.store.baseParams.id_gestion = this.cmbGestion.getValue();
                this.load({params: {start: 0, limit: this.tam_pag}});
            },

            cmbGestion: new Ext.form.ComboBox({
                name: 'gestion',
                //id: 'gestion_reg',
                fieldLabel: 'Gestion',
                allowBlank: true,
                emptyText: 'Gestion...',
                blankText: 'Año',
                editable: false,
                store: new Ext.data.JsonStore(
                    {
                        url: '../../sis_parametros/control/Gestion/listarGestion',
                        id: 'id_gestion',
                        root: 'datos',
                        sortInfo: {
                            field: 'gestion',
                            direction: 'DESC'
                        },
                        totalProperty: 'total',
                        fields: ['id_gestion', 'gestion'],
                        // turn on remote sorting
                        remoteSort: true,
                        baseParams: {par_filtro: 'gestion'}
                    }),
                valueField: 'id_gestion',
                triggerAction: 'all',
                displayField: 'gestion',
                hiddenName: 'id_gestion',
                mode: 'remote',
                pageSize: 50,
                queryDelay: 500,
                listWidth: '280',
                hidden: false,
                width: 80
            }),




            Atributos: [
                {
                    config: {
                        labelSeparator: '',
                        inputType: 'hidden',
                        name: 'id_item_partida'
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        labelSerparator: '',
                        inputType: 'hidden',
                        name: 'id_clasificacion',
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config: {
                        labelSerparator: '',
                        inputType: 'hidden',
                        name: 'id_item',
                    },
                    type: 'Field',
                    form: true
                },
                {
                    config:{
                        name: 'id_gestion',
                        inputType:'hidden'
                    },
                    type:'Field',
                    form:true
                },
                {
                    config: {
                        name: 'id_partida',
                        fieldLabel: 'Partida',
                        typeAhead: false,
                        forceSelection: true,
                        allowBlank: false,
                        emptyText: 'Partida...',
                        store: new Ext.data.JsonStore({
                            url: '../../sis_presupuestos/control/Partida/listarPartida',
                            id: 'id_partida',
                            root: 'datos',
                            sortInfo: {
                                field: 'codigo',
                                direction: 'ASC'
                            },
                            totalProperty: 'total',
                            fields: ['id_partida', 'codigo', 'nombre_partida','descripcion','id_gestion','desc_gestion','tipo','sw_transaccional'],
                            // turn on remote sorting
                            remoteSort: true,
                            baseParams: {par_filtro: 'par.nombre_partida#par.codigo',sw_transaccional:'movimiento', tipo_gasto:'si'}
                        }),
                        sortable:false   ,
                        valueField: 'id_partida',
                        displayField: 'nombre_partida',
                        gdisplayField: 'desc_partida',
                        triggerAction: 'all',
                        lazyRender: true,
                        mode: 'remote',
                        pageSize: 20,
                        queryDelay: 200,
                        listWidth:280,
                        minChars: 2,
                        gwidth: 170,
                        renderer: function(value, p, record) {
                            return String.format('{0}', record.data['desc_partida']);
                        },
                        tpl: '<tpl for="."><div class="x-combo-list-item"><p><b>Codigo: <span style="color:green;">{codigo}</span></b></p><strong><b>Partida:</b> <span style="color:red;">{nombre_partida}</span></strong> <p><b>{tipo} - <span style="color:blue;">{desc_gestion}</span></b></p></div></tpl>'
                    },
                    type: 'ComboBox',
                    id_grupo: 0,
                    filters: {
                        pfiltro: 'par.nombre_partida#par.codigo',
                        type: 'string'
                    },
                    grid: true,
                    form: true
                },
                {
                    config: {
                        name: 'tipo',
                        fieldLabel: 'Tipo',
                        allowBlank: true,
                        width: '100%',
                        gwidth: 80,
                        maxLength: 25,
                        renderer:function (value, p, record){
                            if(value == 'directo'){
                                return String.format('<font color="blue">{0}</font>', value);
                            }
                            else{
                                return String.format('<font color="red">{0}</font>', value);
                            }

                        }
                    },
                    type: 'TextField',
                    filters: {
                        pfiltro: 'cpa.tipo',
                        type: 'string'
                    },
                    id_grupo: 1,
                    grid: true,
                    form: false
                },


                {
                    config: {
                        name: 'estado_reg',
                        fieldLabel: 'Estado Reg.',
                        gwidth: 100,
                        maxLength: 10
                    },
                    type: 'TextField',
                    filters: {
                        pfiltro: 'cpa.estado_reg',
                        type: 'string'
                    },
                    id_grupo: 1,
                    grid: true,
                    form: false
                }, {
                    config: {
                        name: 'fecha_reg',
                        fieldLabel: 'Fecha creación',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {
                        pfiltro: 'cpa.fecha_reg',
                        type: 'date'
                    },
                    id_grupo: 1,
                    grid: true,
                    form: false
                }, {
                    config: {
                        name: 'usr_reg',
                        fieldLabel: 'Creado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'NumberField',
                    filters: {
                        pfiltro: 'usu1.cuenta',
                        type: 'string'
                    },
                    id_grupo: 1,
                    grid: true,
                    form: false
                }, {
                    config: {
                        name: 'fecha_mod',
                        fieldLabel: 'Fecha Modif.',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        format: 'd/m/Y',
                        renderer: function (value, p, record) {
                            return value ? value.dateFormat('d/m/Y H:i:s') : ''
                        }
                    },
                    type: 'DateField',
                    filters: {
                        pfiltro: 'cpa.fecha_mod',
                        type: 'date'
                    },
                    id_grupo: 1,
                    grid: true,
                    form: false
                }, {
                    config: {
                        name: 'usr_mod',
                        fieldLabel: 'Modificado por',
                        allowBlank: true,
                        anchor: '80%',
                        gwidth: 100,
                        maxLength: 4
                    },
                    type: 'NumberField',
                    filters: {
                        pfiltro: 'usu2.cuenta',
                        type: 'string'
                    },
                    id_grupo: 1,
                    grid: true,
                    form: false
                }
            ],
            title: 'Clasificacion Partida',
            ActSave: '../../sis_almacenes/control/ClasificacionPartida/insertarClasificacionPartida',
            ActDel: '../../sis_almacenes/control/ClasificacionPartida/eliminarClasificacionPartida',
            ActList: '../../sis_almacenes/control/ClasificacionPartida/listarClasificacionPartida',
            id_store: 'id_item_partida',
            fields: [
                {name: 'id_item_partida'},
                {name: 'id_clasificacion'},
                {name: 'id_partida'},
                {name: 'tipo'},
                {name: 'desc_partida',type: 'string'},
                {name: 'estado_reg',type: 'string'},
                {name: 'fecha_reg',type: 'date',dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_reg',type: 'numeric'},
                {name: 'fecha_mod',type: 'date',dateFormat: 'Y-m-d H:i:s.u'},
                {name: 'id_usuario_mod',type: 'numeric'},
                {name: 'usr_reg',type: 'string'},
                {name: 'usr_mod',type: 'string'},
                {name: 'id_gestion', type: 'numeric'},
                {name: 'id_item', type: 'numeric'},
            ],

            sortInfo:{
                field: 'id_item_partida',
                direction:'ASC'
            },

            bdel: true,
            bnew: true,
            bsave:false,
            //fwidth:400,

            loadValoresIniciales : function() {
                Phx.vista.ClasificacionPartida.superclass.loadValoresIniciales.call(this);
                //console.log('llegathis', this.getComponente('id_item'))

                this.getComponente('id_gestion').setValue(this.cmbGestion.getValue());
                this.getComponente('id_partida').store.baseParams.id_gestion= this.cmbGestion.getValue();

                console.log('llegathis', this.maestro.id_item)

                if (this.maestro.id_item == undefined){
                    console.log('llegam1val')
                    this.getComponente('id_item').setValue(0);
                    this.getComponente('id_clasificacion').setValue(this.maestro.id_clasificacion);
                }else{
                    console.log('llegam2val')
                    this.getComponente('id_item').setValue(this.maestro.id_item);
                    //this.getComponente('id_clasificacion').setValue('');
                }

                //this.getComponente('id_clasificacion').setValue(this.maestro.id_clasificacion);
                //this.getComponente('id_item').setValue(this.maestro.id_item);



            },
            onReloadPage: function (m) {
                this.maestro = m;
                //console.log('llegamm', this.maestro )

                if (this.maestro.id_item == undefined){
                    console.log('llegam1')
                    this.store.baseParams.id_clasificacion = this.maestro.id_clasificacion;
                    this.store.baseParams.id_item = 0;
                }else{
                    console.log('llegam2')
                    this.store.baseParams.id_item = this.maestro.id_item;
                }


                this.load({params:{start:0, limit:50}})
                //this.load({start: 0,limit: 50});
            },
            preparaMenu: function (n) {
                //Phx.vista.ClasificacionPartida.superclass.preparaMenu.call(this, n);
                var selectedRow = this.sm.getSelected();

                if(selectedRow.data.tipo != 'indirecto'){
                    Phx.vista.ClasificacionPartida.superclass.preparaMenu.call(this);
                 }
                 else{
                    this.getBoton('edit').disable();
                    this.getBoton('del').disable();
                    //this.getBoton('del').disable();
                }
            },

            onButtonEdit: function () {
                //this.accionFormulario = 'EDIT';
                //var data = this.getSelectedData();
                //var data = this.sm.getSelected();
                //console.log('llegaedit', data)
                //this.maestro = m;


                Phx.vista.ClasificacionPartida.superclass.onButtonEdit.call(this);
                //console.log('llegaedit', this.maestro.id_item)
                this.getComponente('id_item').setValue(this.maestro.id_item);

                this.getComponente('id_gestion').setValue(this.cmbGestion.getValue());
                this.getComponente('id_partida').store.baseParams.id_gestion= this.cmbGestion.getValue();

            },


            liberaMenu: function (n) {
                Phx.vista.ClasificacionPartida.superclass.liberaMenu.call(this, n);

            },


            fheight: '60%',
            fwidth:'60%'
        }
    );
</script>