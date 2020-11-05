class Grupo{
    id?:number = null;
    descripcion?:string = null;
    codigo?:string = null;
    status?:boolean = null;
    constructor({id, descripcion, codigo, status} : namedParametersGrupo){
        this.id = (id) ? id : null;
        this.descripcion = (descripcion) ? descripcion : null;
        this.codigo = (codigo) ? codigo : null;
        this.status = (status) ? status : null;
    }

    static fromMap(data){
        let id:number = (data.id) ? data.id : 0;
        let descripcion:string = (data.descripcion) ? data.descripcion : '';
        let codigo:string = (data.codigo) ? data.codigo : '';
        let status:boolean = (data.status) ? (data.status == 1) ? true : false : false;
        return new Grupo({id: id, descripcion: descripcion, codigo: codigo, status: status});
    }

    toJson(){
        return {
            "id" : this.id,
            "descripcion" : this.descripcion,
            "codigo" : this.codigo,
            "status" : this.status,
        };
    }
}

let formKey2 = new FormGlobalKey();
let _streamController = new StreamController();
let setState2:any;
let _checkbox:boolean = false;
let _ckbStatus:boolean = false;
let _mostrarVentanaEditar = false;
var _txtGrupo = new TextEditingController();
var _txtCodigo = new TextEditingController();
let _grupo:Grupo = new Grupo({});
let listaGrupo:Grupo[] = [];
async function _guardar(){
    
    
    if(formKey2.validate()){
        _grupo.descripcion = _txtGrupo.text;
        _grupo.codigo = _txtCodigo.text;
        _grupo.status = _ckbStatus;

        let data:any = {
            "servidor" : servidorGlobal,
            "idUsuario" : idUsuario,
            "grupo" : _grupo.toJson(),
        }
        console.log("Dentro _guardarrrrrrrrrrrrr: ", data);
    //     var jwt = createJWT(data);
        var jwt = createJWT(data);

        var json = await http.post({url: `${rutaGlobal}/api/grupos/guardar`, headers: Utils.headers, data: {"datos" : jwt}});
        _grupo = Grupo.fromMap(json.grupo);
        _mostrarVentanaEditar = false;
        setState2();
        console.log("Grupo.ts guardar: ", json);
        
    }
    

}

function _ventanaMostrar(){
    return  Container({
        style: new TextStyle({padding: EdgetInsets.only({top: 0}),}),
        child: Column({
            children: [
                Row({
                    children: [
                        CardTop({
                            text: "Todos los grupos",
                            cargando: _cargando
                        }),
                        // Texto("Grupos", new TextStyle({fontSize: 16}))
                    ]
                }),
                Row({
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                        Padding({
                            padding: EdgetInsets.only({right: 10}),
                            child: RaisedButton({
                                color: "#4caf50",
                                child: Texto("AGREGAR GRUPO", new TextStyle({fontSize: 12, color: "white", fontWeight: FontWeight.w500})),
                                onPressed: ()=>{
                                    _mostrarVentanaEditar = true;
                                    setState2();
                                }
                            })
                        })
                    ]
                }),
                // Texto("Grupos", new TextStyle({fontSize: 18, textAlign: TextAlign.center})),
                // CardTranslate3D({
                //     text: "AGREGAR  |  EDITAR" 
                // }),
                CheckBox({
                    value: _checkbox,
                    onChanged: (value) => {
                        _checkbox = value;
                        console.log("grupo.ts onChanged: ", _checkbox);
                        
                        setState2();
                    }
                }),
                StreamBuilder({
                    stream: _streamController,
                    builder: (id:any, snapshot:any)=>{
                        if(snapshot)
                            return _buildTable(listaGrupo)
                        else
                            return _buildTable([]);
                    }
                }),
                
            ]
        })
    });
}

function _ventanaEditar(){
    return Column({
        children: [
           
            Row({
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                    Align({
                        alignment: Alignment.start,
                        child: Padding({
                            padding: EdgetInsets.only({left: 20, top: 10}),
                            child: BackRoundedButton({
                                icon: Icon(Icons.arrow_back, "white"),
                                onTap: () => {
                                    _mostrarVentanaEditar = false;
                                    setState2();
                                }
                            })
                        })
                    }),
                    Padding({padding:EdgetInsets.only({bottom: 15, top: 15}),child: Texto("Grupos", new TextStyle({fontSize: 20, fontWeight: FontWeight.w300, textAlign: TextAlign.center}))})
                ]
            }),
            Padding({
                padding: EdgetInsets.only({top: 10}),
                child: CardTranslate3D({
                    text: "AGREGAR  |  EDITAR"
                })
            }),
            SizedBox({height: 20}),
            Form({
                key: formKey2,
                child: 
                Center({
                    child: ResizedContainer({
                        child: 
                        
                        Wrap({
                            children: [
                                // Container({
                                //     style: new TextStyle({background: "red", width: 2000, height: 50})
                                // }),
                                // Container({
                                //     style: new TextStyle({background: "blue", width: 2000, height: 50})
                                // }),
                                MyTextFormField({
                                    controller: _txtGrupo,
                                    labelText: "Grupo",
                                    icon: Icons.group_work,
                                    sm: 1,
                                }),
                                MyTextFormField({
                                    controller: _txtCodigo,
                                    labelText: "Codigo",
                                    icon: Icons.source,
                                    xs: 2,
                                    sm: 2,
                                }),
                                Padding({
                                    padding: EdgetInsets.only({left: 10, top: 15}),
                                    child: CheckBox({
                                        value: _ckbStatus,
                                        labelText: "Activo",
                                        onChanged: (data) => {
                                            _ckbStatus = data;
                                            setState2();
                                        }
                                    })
                                }),
                                Row({
                                    mainAxisAlignment: MainAxisAlignment.end,
                                    children: [
                                        Padding({
                                            padding: EdgetInsets.only({right: 0}),
                                            child: RaisedButton({
                                                child: Texto("Guardar", new TextStyle({color: "white"})),
                                                onPressed: () => {
                                                    _guardar();
                                                }
                                            })
                                        })
                                    ]
                                })
                            ]
                        })
                    
                    })
                }),
                
                
            })
            ,
            
            // MyTextFormField({
            //     controller: _txtGrupo,
            //     labelText: "Grupo",
            //     icon: Icons.group_work,
            //     sm: 1,
            // }),
            // MyTextFormField({
            //     controller: _txtCodigo,
            //     labelText: "Codigo",
            //     icon: Icons.source,
            //     sm: 1.77,
            // }),
            // Center({
            //     child: MyTextFormField({
            //         controller: _txtGrupo,
            //         labelText: "Grupo",
            //         icon: Icons.group_work,
            //         sm: 1.4,
            //     })
            // }),
            // Center({
            //     child: Row({
            //         mainAxisAlignment: MainAxisAlignment.center,
            //         children: [
            //             MyTextFormField({
            //                 controller: _txtCodigo,
            //                 labelText: "Codigo",
            //                 icon: Icons.source,
            //                 sm: 1.77,
            //             }),
            //             Padding({
            //                 padding: EdgetInsets.only({left: 10, top: 10}),
            //                 child: CheckBox({
            //                     value: _ckbStatus,
            //                     labelText: "Activo",
            //                     onChanged: (data) => {
            //                         _ckbStatus = data;
            //                         setState2();
            //                     }
            //                 })
            //             })
            //         ]
            //     })
            // })
        ]
    })
}

function _buildTable(listaGrupo:Grupo[]){
    if(listaGrupo.length > 0){
        return DataTable({
            resultarFilasImpares: true,
            columns: [
                new DataColumn({label: Texto("index", new TextStyle({fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300}))}),
                new DataColumn({label: Texto("Descripcion",  new TextStyle({fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300}))}),
                new DataColumn({label: Texto("Estado",  new TextStyle({fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300}))}),
            ],
            rows: listaGrupo.map((element) =>  new DataRow({
                cells: [
                    new DataCell(Texto(element.id, new TextStyle({textAlign: TextAlign.center}))),
                    new DataCell(Texto(element.descripcion, new TextStyle({textAlign: TextAlign.center}))),
                    new DataCell(Texto(element.codigo, new TextStyle({textAlign: TextAlign.center}))),
                ]
            })
            )
        })
    }
    else{
        return DataTable({
            resultarFilasImpares: true,
            columns: [
                new DataColumn({label: Texto("index", new TextStyle({fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300}))}),
                new DataColumn({label: Texto("Descripcion",  new TextStyle({fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300}))}),
                new DataColumn({label: Texto("Estado",  new TextStyle({fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300}))}),
            ],
            rows: []
        });
    }
}

interface namedParametersVentana{
    grupo?:any;
    editar?:boolean;
}

function _ventana({grupo, editar = false} : namedParametersVentana){
    if(editar)
        return _ventanaEditar();
    else
    return _ventanaMostrar();
}

Builder({
    id: "containerWizard",
    initState: () => {
        var jwt = createJWT({"servidor" : servidorGlobal, "idUsuario" : idUsuario});
        _cargando = true;
        let response = http.get({url:`${rutaGlobal}/api/grupos/get?token=${jwt}`, headers: Utils.headers}).then((json) => {
            console.log("response: ", json);
            listaGrupo = json.grupos;
            _streamController.add(listaGrupo);
            _cargando = false;
            setState2();
            // // listaBanca = json.bancas;
            // // listaSorteo = json.sorteos;
            // // listaLoteria = json.loterias;
            // // listaMoneda = json.monedas;
            // // _streamController.add(listaBanca);
            // // _streamControllerSorteo.add(listaSorteo);
            // // _streamControllerLoteria.add(listaLoteria);
            // // _streamControllerMoneda.add(listaMoneda);
            // console.log("response listaBanca: ", listaLoteria);
        });
    },
    builder: (id:any, setStateFromBuilder:any) => {
        setState2 = setStateFromBuilder;
        console.log("Inside grupos");
        
        return Init({
            id: id,
            initDefaultStyle: true,
            child: 
            // Container({child: Texto("Holaaa soy un tipo vacacino")})
            _ventana({editar: _mostrarVentanaEditar, grupo: _grupo})
        });
    }
})



interface namedParametersGrupo{
    id?:number;
    descripcion?:string;
    codigo?:string;
    status?:boolean;
}

