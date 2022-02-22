<?php
/**
 *@package pXP
 *@file MovimientoAlm.php
 *@author  Gonzalo Sarmiento
 *@date 10-07-2013 10:22:05
 *@description Archivo con la interfaz de usuario que permite la ejecucion de todas las funcionalidades del sistema
 */
header("content-type: text/javascript; charset=UTF-8");
?>
<style type="text/css" rel="stylesheet">
    .x-selectable,
    .x-selectable * {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }

    .x-grid-row td,
    .x-grid-summary-row td,
    .x-grid-cell-text,
    .x-grid-hd-text,
    .x-grid-hd,
    .x-grid-row,

    .x-grid-row,
    .x-grid-cell,
    .x-unselectable
    {
        -moz-user-select: text !important;
        -khtml-user-select: text !important;
        -webkit-user-select: text !important;
    }
</style>
<script>
    Phx.vista.MovimientoAlm = {
        bedit:false,
        bnew:false,
        bsave:false,
        bdel:false,
        require:'../../../sis_almacenes/vista/movimiento/Movimiento.php',
        requireclase:'Phx.vista.Movimiento',
        title:'Movimiento',
        nombreVista: 'movimientoAlm',
        viewConfig: {
            stripeRows: false,
            getRowClass: function(record) {
                return "x-selectable";
            }/*,
            listeners:{
                itemkeydown:function(view, record, item, index, e){
                    alert('The press key is' + e.getKey());
                }
            }*/

        },
        constructor: function(config) {
            this.maestro=config.maestro;
            Phx.vista.MovimientoAlm.superclass.constructor.call(this,config);
            this.addButton('ini_estado',{argument: {operacion: 'inicio'},text:'Dev. a Borrador',iconCls: 'batras',disabled:true,handler:this.retroceder,tooltip: '<b>Retorna Movimiento al estado borrador</b>'});
            this.addButton('ant_estado',{argument: {operacion: 'anterior'},text:'Anterior',iconCls: 'batras',disabled:true,handler:this.retroceder,tooltip: '<b>Pasar al Anterior Estado</b>'});
            this.addButton('sig_estado',{text:'Finalizar',iconCls: 'badelante',disabled:true,handler:this.fin_requerimiento,tooltip: '<b>Finalizar Registro</b>'});
            this.addButton('fin_grupo',{text:'Finalizar Grupo',iconCls: 'badelante',disabled:true,handler:this.fin_grupo,tooltip: '<b>Finalizar Grupo</b>'});
            this.addButton('comail',{text:'Comail y Fecha Ingreso/Salida',iconCls: 'bsendmail',disabled:true,handler:this.onRegistrarComail,tooltip: '<b>Agregar numero comail y fecha ingreso/salida</b>'});
            this.getBoton('btnRevertir').hide();
            this.getBoton('btnCancelar').hide();
            this.store.baseParams={tipo_interfaz:this.nombreVista};
            this.load({params:{start:0, limit:this.tam_pag}});
            this.iniciarEventos();
            //Creación de ventana para workflow
            this.crearVentanaWF();
        },
        iniciarEventos:function(){
            this.cmpFechaMov = this.getComponente('fecha_mov');
            this.cmpIdGestion = this.getComponente('id_gestion');
            this.cmpMovimientoTipo = this.getComponente('tipo');
            this.cmpSubtipoMovimiento = this.getComponente('id_movimiento_tipo');
            this.cmpAlmacen = this.getComponente('id_almacen');
            this.cmpDescripcion = this.getComponente('descripcion');
            this.cmpTipoSolicitante = this.getComponente('solicitante');
            this.cmpFuncionario = this.getComponente('id_funcionario');
            this.cmpObservaciones = this.getComponente('observaciones');
            this.cmpComail = this.getComponente('comail');
            this.cmpFechaSalida = this.getComponente('fecha_salida');

            this.grid.addListener('keydown', this.selectTodos,this);
        },
        selectTodos: function(e){
            if(e.shiftKey && e.getKey()==83) {
                this.grid.getSelectionModel().selectAll();
            }
        },
        onButtonAct:function(){
            Phx.vista.MovimientoAlm.superclass.onButtonAct.call(this);
            this.grid.getSelectionModel().clearSelections(true);
        },
        onRegistrarComail:function(){
            this.onButtonEdit();
            this.ocultarComponente(this.cmpMovimientoTipo);
            this.ocultarComponente(this.cmpFechaMov);
            this.ocultarComponente(this.cmpSubtipoMovimiento);
            this.ocultarComponente(this.cmpAlmacen);
            this.ocultarComponente(this.cmpDescripcion);
            this.ocultarComponente(this.cmpObservaciones);
            this.ocultarComponente(this.cmpTipoSolicitante);
            this.ocultarComponente(this.cmpFuncionario);
            this.mostrarComponente(this.cmpComail);
            this.mostrarComponente(this.cmpFechaSalida);
        },
        onButtonEdit:function(){

            var tb = Phx.vista.MovimientoAlm.superclass.onButtonEdit.call(this);

            var records = this.grid.getSelectionModel().getSelections();
            var rec = '';
            Ext.each(records, function(record, index) {
                if(rec != ''){
                    rec = rec +','+record.id;
                }else{
                    rec = record.id
                }
            });
            this.argumentExtraSubmit = {
                'registros': rec
            };
            /*this.mostrarComponente(this.cmpMovimientoTipo);
             this.mostrarComponente(this.cmpFechaMov);
             this.mostrarComponente(this.cmpSubtipoMovimiento);
             this.mostrarComponente(this.cmpAlmacen);
             this.mostrarComponente(this.cmpDescripcion);
             this.mostrarComponente(this.cmpObservaciones);
             this.ocultarComponente(this.cmpComail);*/
        },
        sigEstado:function(){
            var d= this.sm.getSelected().data;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_almacenes/control/Movimiento/finalizarMovimiento',
                params:{id_movimiento:d.id_movimiento,
                    id_almacen:d.id_almacen,
                    fecha_mov:d.fecha_mov,
                    operacion:'finalizarMovimiento'},
                success:this.successSinc,
                argument:{data:d},
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        antEstado:function(res,eve) {
            //Oculta y deshablita controles
            this.cmbTipoEstWF.hide();
            this.cmbTipoEstWF.disable();
            this.cmbFunWF.hide();
            this.cmbFunWF.disable();
            //Muestra y habilita el campo de observaciones
            this.txtObs.show();
            this.txtObs.allowBlank=false;
            this.txtObs.setValue('');
            this.sw_estado =res.argument.estado;
            //Oculta botones de cancelar y muestra la ventana
            this.winWF.buttons[1].hide();
            this.winWF.buttons[0].show();
            this.winWF.show();
        },
        fin_grupo: function(){
            //var d= this.sm.getSelected().data;
            var filas=this.sm.getSelections(),
                total= 0,tmpMovimientos='', tmpAlmacenes='', me = this;
            for(var i=0;i<this.sm.getCount();i++){
                aux={};
                if(total == 0){
                    tmpMovimientos = filas[i].data.id_movimiento;
                    tmpAlmacenes = filas[i].data.id_almacen;
                }
                else{
                    tmpMovimientos = tmpMovimientos + ','+ filas[i].data.id_movimiento;
                    tmpAlmacenes = tmpAlmacenes + ','+ filas[i].data.id_almacen;
                }
                total = total + 1;
            }
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url: '../../sis_almacenes/control/Movimiento/finalizarGrupo',
                params: {id_movimientos: tmpMovimientos, id_almacen: tmpAlmacenes, operacion: 'verificar'},
                success: this.onFinalizarGrupo,
                failure: this.conexionFailure,
                timeout: this.timeout,
                scope: this
            });
        },
        onFinalizarGrupo:function(resp){
            Phx.CP.loadingHide();
            //var d= this.sm.getSelected().data;
            var filas=this.sm.getSelections(),
                total= 0,tmpMovimientos='', tmpAlmacenes='', me = this;
            for(var i=0;i<this.sm.getCount();i++){
                aux={};
                if(total == 0){
                    tmpMovimientos = filas[i].data.id_movimiento;
                    tmpAlmacenes = filas[i].data.id_almacen;
                }
                else{
                    tmpMovimientos = tmpMovimientos + ','+ filas[i].data.id_movimiento;
                    tmpAlmacenes = tmpAlmacenes + ','+ filas[i].data.id_almacen;
                }
                total = total + 1;
            }
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            var swWin=0;
            var swFun=0;
            var swEst=0;
            //console.log(reg)
            //Se verifica la respuesta de la verificación
            if(!reg.ROOT.error){
                var data=reg.ROOT.datos;
                //Verifica si hay alertas y pregunta si continuar
                console.log(data);
                if(data.alertas!=''){
                    if(data.permitir_sin_saldo == 'si'){
                        var v_aux = data.alertas+'\n\n¿Desea continuar de todos modos?';
                        if(!confirm(v_aux)){
                            return;
                        }
                    }else{
                        alert(data.alertas);
                        return;
                    }
                }
                //Se hace la llamda directa porque el WF no tiene bifurcaciones
                Phx.CP.loadingShow();
                Ext.Ajax.request({
                    url:'../../sis_almacenes/control/Movimiento/finalizarGrupo',
                    params:{
                        id_movimientos:tmpMovimientos,
                        operacion:'siguiente',
                        id_funcionario_wf:data.id_funcionario_wf,
                        id_tipo_estado: data.id_tipo_estado_wf,
                        id_almacenes: tmpAlmacenes
                    },
                    success:this.successFinSol,
                    failure: this.conexionFailure,
                    timeout:this.timeout,
                    scope:this
                });
            } else{
                alert('ocurrio un error durante el proceso')
            }
        },
        onWF: function(){
            if(this.sw_estado=='inicio'){
            } else if(this.sw_estado=='inicio'){
            } else {
                Phx.vista.MovimientoVb.superclass.onWF.call(this);
            }
        },
        antEstadoSubmmit:function(res){
            var d= this.sm.getSelected().data;
            Phx.CP.loadingShow();
            var operacion = 'cambiar';
            operacion=  this.sw_estado == 'inicio'?'inicio':operacion;
            Ext.Ajax.request({
                // form:this.form.getForm().getEl(),
                url:'../../sis_almacenes/control/Movimiento/anteriorEstadoMovimiento',
                params:{id_movimiento:d.id_movimiento,
                    id_estado_wf:d.id_estado_wf,
                    operacion: operacion,
                    obs:this.cmpObs.getValue()},
                success:this.successSinc,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        successSinc:function(resp){
            Phx.CP.loadingHide();
            var reg = Ext.util.JSON.decode(Ext.util.Format.trim(resp.responseText));
            if(!reg.ROOT.error){
                if (reg.ROOT.datos.operacion=='preguntar_todo'){
                    if(reg.ROOT.datos.num_estados==1 && reg.ROOT.datos.num_funcionarios==1){
                        //directamente mandamos los datos
                        Phx.CP.loadingShow();
                        var d= this.sm.getSelected().data;
                        Ext.Ajax.request({
                            // form:this.form.getForm().getEl(),
                            url:'../../sis_almacenes/control/Movimiento/siguienteEstadoMovimiento',
                            params:{id_movimiento:d.id_movimiento,
                                operacion:'cambiar',
                                id_tipo_estado:reg.ROOT.datos.id_tipo_estado,
                                id_funcionario:reg.ROOT.datos.id_funcionario_estado,
                                id_depto:reg.ROOT.datos.id_depto_estado,
                                //id_solicitud:d.id_solicitud,
                                obs:this.cmpObs.getValue(),
                                instruc_rpc:this.cmbIntrucRPC.getValue()
                            },
                            success:this.successSinc,
                            failure: this.conexionFailure,
                            timeout:this.timeout,
                            scope:this
                        });
                    }
                    else{
                        this.cmbTipoEstado.store.baseParams.estados= reg.ROOT.datos.estados;
                        this.cmbTipoEstado.modificado=true;
                        console.log(resp)
                        if(resp.argument.data.estado=='vbrpc'){
                            this.cmbIntrucRPC.show();
                            this.cmbIntrucRPC.enable();
                        }
                        else{
                            this.cmbIntrucRPC.hide();
                            this.cmbIntrucRPC.disable();
                        }
                        this.cmpObs.setValue('');
                        this.cmbFuncionarioWf.disable();
                        this.wEstado.buttons[1].hide();
                        this.wEstado.buttons[0].show();
                        this.wEstado.show();
                    }
                }
                if (reg.ROOT.datos.operacion=='cambio_exitoso'){
                    this.reload();
                    this.wEstado.hide();
                }
            } else{
                alert('ocurrio un error durante el proceso')
            }
        },
        preparaMenu:function(n){
            var data = this.getSelectedData();
            var tb =this.tbar;
            Phx.vista.MovimientoAlm.superclass.preparaMenu.call(this,n);

            if(data.estado =='aprobado' ){
                this.getBoton('sig_estado').disable();
                this.getBoton('fin_grupo').disable();
            }
            if(data.estado =='proceso'){
                this.getBoton('sig_estado').disable();
                this.getBoton('fin_grupo').disable();
            }
            if(data.estado !='aprobado' && data.estado !='proceso' ){
                this.getBoton('ini_estado').enable();
                this.getBoton('ant_estado').enable();
                if ( data.estado_mov != 'autorizacion' && data.estado_mov != 'vbarea' ) {
                    this.getBoton('sig_estado').enable();
                    this.getBoton('fin_grupo').enable();
                }
                this.getBoton('comail').enable();
            }
            if(data.codigo_tran.length != 0){
                this.getBoton('ini_estado').disable();
                this.getBoton('ant_estado').disable();
            }
            return tb
        },
        liberaMenu:function(){
            var tb = Phx.vista.MovimientoAlm.superclass.liberaMenu.call(this);
            if(tb){
                this.getBoton('ini_estado').disable();
                this.getBoton('ant_estado').disable();
                this.getBoton('sig_estado').disable();
                this.getBoton('fin_grupo').disable();
            }
            return tb
        },
        south:
            {
                url:'../../../sis_almacenes/vista/movimientoDetalle/MovimientoDetalleAlm.php',
                title:'Detalle',
                height:'50%',
                cls:'MovimientoDetalleAlm'
            },
        retroceder: function(resp){
            console.log(resp)
            var d= this.sm.getSelected().data;
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url:'../../sis_almacenes/control/Movimiento/finalizarMovimiento',
                params:{
                    id_movimiento:d.id_movimiento,
                    operacion:resp.argument.operacion,
                    //id_funcionario_wf:data.wf_id_funcionario,
                    //id_tipo_estado: data.wf_id_tipo_estado,
                    id_almacen: d.id_almacen,
                    obs: this.txtObs.getValue()
                },
                success:this.successFinSol,
                failure: this.conexionFailure,
                timeout:this.timeout,
                scope:this
            });
        },
        generaReporte: function(){
            var rec = this.sm.getSelected();
            Phx.CP.loadingShow();
            Ext.Ajax.request({
                url : '../../sis_almacenes/control/Movimiento/generarReporteMovimiento',
                params : {
                    'id_movimiento' : rec.data.id_movimiento,
                    'costos': 'no'
                },
                success : this.successExport,
                failure : this.conexionFailure,
                timeout : this.timeout,
                scope : this
            });
        }
    };
</script>