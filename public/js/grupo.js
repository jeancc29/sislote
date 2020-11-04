var setState2;
var _checkbox = false;
var _ckbStatus = false;
var _mostrarVentanaEditar = true;
var _txtGrupo = new TextEditingController();
var _txtCodigo = new TextEditingController();
function _ventanaMostrar() {
    return Container({
        style: new TextStyle({ padding: EdgetInsets.only({ top: 0 }) }),
        child: Column({
            children: [
                Row({
                    children: [
                        CardTop({
                            text: "Todos los grupos"
                        }),
                    ]
                }),
                Row({
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                        Padding({
                            padding: EdgetInsets.only({ right: 10 }),
                            child: RaisedButton({
                                color: "#4caf50",
                                child: Texto("AGREGAR GRUPO", new TextStyle({ fontSize: 12, color: "white", fontWeight: FontWeight.w500 })),
                                onPressed: function () {
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
                    onChanged: function (value) {
                        _checkbox = value;
                        console.log("grupo.ts onChanged: ", _checkbox);
                        setState2();
                    }
                }),
                DataTable({
                    resultarFilasImpares: true,
                    columns: [
                        new DataColumn({ label: Texto("index", new TextStyle({ fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300 })) }),
                        new DataColumn({ label: Texto("Descripcion", new TextStyle({ fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300 })) }),
                        new DataColumn({ label: Texto("Estado", new TextStyle({ fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300 })) }),
                    ],
                    rows: [
                        new DataRow({
                            cells: [
                                new DataCell(Texto("1", new TextStyle({ textAlign: TextAlign.center }))),
                                new DataCell(Texto("Grupo 20", new TextStyle({ textAlign: TextAlign.center }))),
                                new DataCell(Texto("Activo", new TextStyle({ textAlign: TextAlign.center }))),
                            ]
                        }),
                        new DataRow({
                            cells: [
                                new DataCell(Texto("1", new TextStyle({ textAlign: TextAlign.center }))),
                                new DataCell(Texto("Grupo 20", new TextStyle({ textAlign: TextAlign.center }))),
                                new DataCell(Texto("Activo", new TextStyle({ textAlign: TextAlign.center }))),
                            ]
                        }),
                        new DataRow({
                            cells: [
                                new DataCell(Texto("1", new TextStyle({ textAlign: TextAlign.center }))),
                                new DataCell(Texto("Grupo 20", new TextStyle({ textAlign: TextAlign.center }))),
                                new DataCell(Texto("Activo", new TextStyle({ textAlign: TextAlign.center }))),
                            ]
                        })
                    ]
                })
            ]
        })
    });
}
function _ventanaEditar() {
    return Column({
        children: [
            Row({
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                    Align({
                        alignment: Alignment.start,
                        child: Padding({
                            padding: EdgetInsets.only({ left: 20, top: 10 }),
                            child: BackRoundedButton({
                                icon: Icon(Icons.arrow_back, "white")
                            })
                        })
                    }),
                    Padding({ padding: EdgetInsets.only({ bottom: 15, top: 15 }), child: Texto("Grupos", new TextStyle({ fontSize: 20, fontWeight: FontWeight.w300, textAlign: TextAlign.center })) })
                ]
            }),
            Padding({
                padding: EdgetInsets.only({ top: 10 }),
                child: CardTranslate3D({
                    text: "AGREGAR  |  EDITAR"
                })
            }),
            SizedBox({ height: 20 }),
            Center({
                child: ResizedContainer({
                    child: Wrap({
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
                                sm: 1
                            }),
                            MyTextFormField({
                                controller: _txtCodigo,
                                labelText: "Codigo",
                                icon: Icons.source,
                                sm: 2
                            }),
                            Padding({
                                padding: EdgetInsets.only({ left: 10, top: 10 }),
                                child: CheckBox({
                                    value: _ckbStatus,
                                    labelText: "Activo",
                                    onChanged: function (data) {
                                        _ckbStatus = data;
                                        setState2();
                                    }
                                })
                            })
                        ]
                    })
                })
            }),
            Wrap({
                children: [
                    Container({
                        style: new TextStyle({ background: "red", width: 300, height: 50 })
                    }),
                    Container({
                        style: new TextStyle({ background: "blue", width: 600, height: 50 })
                    }),
                ]
            }),
        ]
    });
}
function _ventana(_a) {
    var grupo = _a.grupo, _b = _a.editar, editar = _b === void 0 ? false : _b;
    if (editar)
        return _ventanaEditar();
    else
        return _ventanaMostrar();
}
Builder({
    id: "containerWizard",
    builder: function (id, setStateFromBuilder) {
        setState2 = setStateFromBuilder;
        console.log("Inside grupos");
        return Init({
            id: id,
            initDefaultStyle: true,
            child: 
            // Container({child: Texto("Holaaa soy un tipo vacacino")})
            _ventana({ editar: _mostrarVentanaEditar })
        });
    }
});
