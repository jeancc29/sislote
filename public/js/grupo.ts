let setState2:any;
let _checkbox:boolean = false;
let _mostrarVentanaEditar = true;

function _ventanaMostrar(){
    return  Container({
        style: new TextStyle({padding: EdgetInsets.only({top: 0}),}),
        child: Column({
            children: [
                Row({
                    children: [
                        CardTop({
                            text: "Todos los grupos",
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
                DataTable({
                    resultarFilasImpares: true,
                    columns: [
                        new DataColumn({label: Texto("index", new TextStyle({fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300}))}),
                        new DataColumn({label: Texto("Descripcion",  new TextStyle({fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300}))}),
                        new DataColumn({label: Texto("Estado",  new TextStyle({fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300}))}),
                    ],
                    rows:[
                        new DataRow({
                            cells: [
                                new DataCell(Texto("1", new TextStyle({textAlign: TextAlign.center}))),
                                new DataCell(Texto("Grupo 20", new TextStyle({textAlign: TextAlign.center}))),
                                new DataCell(Texto("Activo", new TextStyle({textAlign: TextAlign.center}))),
                            ]
                        }),
                        new DataRow({
                            cells: [
                                new DataCell(Texto("1", new TextStyle({textAlign: TextAlign.center}))),
                                new DataCell(Texto("Grupo 20", new TextStyle({textAlign: TextAlign.center}))),
                                new DataCell(Texto("Activo", new TextStyle({textAlign: TextAlign.center}))),
                            ]
                        }),
                        new DataRow({
                            cells: [
                                new DataCell(Texto("1", new TextStyle({textAlign: TextAlign.center}))),
                                new DataCell(Texto("Grupo 20", new TextStyle({textAlign: TextAlign.center}))),
                                new DataCell(Texto("Activo", new TextStyle({textAlign: TextAlign.center}))),
                            ]
                        })
                        
                    ]
                })
            ]
        })
    });
}

function _ventanaEditar(){
    return Column({
        children: [
            Texto("Grupos", new TextStyle({fontSize: 20, fontWeight: FontWeight.w300})),
            CardTranslate3D({
                text: "AGREGAR  |  EDITAR"
            })
        ]
    })
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
    builder: (id:any, setStateFromBuilder:any) => {
        setState2 = setStateFromBuilder;
        console.log("Inside grupos");
        
        return Init({
            id: id,
            initDefaultStyle: true,
            child: 
            // Container({child: Texto("Holaaa soy un tipo vacacino")})
            _ventana({editar: true})
        });
    }
})