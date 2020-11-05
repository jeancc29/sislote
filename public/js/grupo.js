var __awaiter = (this && this.__awaiter) || function (thisArg, _arguments, P, generator) {
    function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
    return new (P || (P = Promise))(function (resolve, reject) {
        function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
        function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
        function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
        step((generator = generator.apply(thisArg, _arguments || [])).next());
    });
};
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [op[0] & 2, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
var Grupo = /** @class */ (function () {
    function Grupo(_a) {
        var id = _a.id, descripcion = _a.descripcion, codigo = _a.codigo, status = _a.status;
        this.id = null;
        this.descripcion = null;
        this.codigo = null;
        this.status = null;
        this.id = (id) ? id : null;
        this.descripcion = (descripcion) ? descripcion : null;
        this.codigo = (codigo) ? codigo : null;
        this.status = (status) ? status : null;
    }
    Grupo.fromMap = function (data) {
        var id = (data.id) ? data.id : 0;
        var descripcion = (data.descripcion) ? data.descripcion : '';
        var codigo = (data.codigo) ? data.codigo : '';
        var status = (data.status) ? (data.status == 1) ? true : false : false;
        return new Grupo({ id: id, descripcion: descripcion, codigo: codigo, status: status });
    };
    Grupo.prototype.toJson = function () {
        return {
            "id": this.id,
            "descripcion": this.descripcion,
            "codigo": this.codigo,
            "status": this.status
        };
    };
    return Grupo;
}());
var formKey2 = new FormGlobalKey();
var _streamController = new StreamController();
var setState2;
var _checkbox = false;
var _ckbStatus = false;
var _mostrarVentanaEditar = false;
var _txtGrupo = new TextEditingController();
var _txtCodigo = new TextEditingController();
var _grupo = new Grupo({});
var listaGrupo = [];
function _guardar() {
    return __awaiter(this, void 0, void 0, function () {
        var data, jwt, json;
        return __generator(this, function (_a) {
            switch (_a.label) {
                case 0:
                    if (!formKey2.validate()) return [3 /*break*/, 2];
                    _grupo.descripcion = _txtGrupo.text;
                    _grupo.codigo = _txtCodigo.text;
                    _grupo.status = _ckbStatus;
                    data = {
                        "servidor": servidorGlobal,
                        "idUsuario": idUsuario,
                        "grupo": _grupo.toJson()
                    };
                    console.log("Dentro _guardarrrrrrrrrrrrr: ", data);
                    jwt = createJWT(data);
                    return [4 /*yield*/, http.post({ url: rutaGlobal + "/api/grupos/guardar", headers: Utils.headers, data: { "datos": jwt } })];
                case 1:
                    json = _a.sent();
                    _grupo = Grupo.fromMap(json.grupo);
                    _mostrarVentanaEditar = false;
                    setState2();
                    console.log("Grupo.ts guardar: ", json);
                    _a.label = 2;
                case 2: return [2 /*return*/];
            }
        });
    });
}
function _ventanaMostrar() {
    return Container({
        style: new TextStyle({ padding: EdgetInsets.only({ top: 0 }) }),
        child: Column({
            children: [
                Row({
                    children: [
                        CardTop({
                            text: "Todos los grupos",
                            cargando: _cargando
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
                    onChanged: function (value) {
                        _checkbox = value;
                        console.log("grupo.ts onChanged: ", _checkbox);
                        setState2();
                    }
                }),
                StreamBuilder({
                    stream: _streamController,
                    builder: function (id, snapshot) {
                        if (snapshot)
                            return _buildTable(listaGrupo);
                        else
                            return _buildTable([]);
                    }
                }),
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
                                icon: Icon(Icons.arrow_back, "white"),
                                onTap: function () {
                                    _mostrarVentanaEditar = false;
                                    setState2();
                                }
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
            Form({
                key: formKey2,
                child: Center({
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
                                    xs: 2,
                                    sm: 2
                                }),
                                Padding({
                                    padding: EdgetInsets.only({ left: 10, top: 15 }),
                                    child: CheckBox({
                                        value: _ckbStatus,
                                        labelText: "Activo",
                                        onChanged: function (data) {
                                            _ckbStatus = data;
                                            setState2();
                                        }
                                    })
                                }),
                                Row({
                                    mainAxisAlignment: MainAxisAlignment.end,
                                    children: [
                                        Padding({
                                            padding: EdgetInsets.only({ right: 0 }),
                                            child: RaisedButton({
                                                child: Texto("Guardar", new TextStyle({ color: "white" })),
                                                onPressed: function () {
                                                    _guardar();
                                                }
                                            })
                                        })
                                    ]
                                })
                            ]
                        })
                    })
                })
            }),
        ]
    });
}
function _buildTable(listaGrupo) {
    if (listaGrupo.length > 0) {
        return DataTable({
            resultarFilasImpares: true,
            columns: [
                new DataColumn({ label: Texto("index", new TextStyle({ fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300 })) }),
                new DataColumn({ label: Texto("Descripcion", new TextStyle({ fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300 })) }),
                new DataColumn({ label: Texto("Estado", new TextStyle({ fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300 })) }),
            ],
            rows: listaGrupo.map(function (element) { return new DataRow({
                cells: [
                    new DataCell(Texto(element.id, new TextStyle({ textAlign: TextAlign.center }))),
                    new DataCell(Texto(element.descripcion, new TextStyle({ textAlign: TextAlign.center }))),
                    new DataCell(Texto(element.codigo, new TextStyle({ textAlign: TextAlign.center }))),
                ]
            }); })
        });
    }
    else {
        return DataTable({
            resultarFilasImpares: true,
            columns: [
                new DataColumn({ label: Texto("index", new TextStyle({ fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300 })) }),
                new DataColumn({ label: Texto("Descripcion", new TextStyle({ fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300 })) }),
                new DataColumn({ label: Texto("Estado", new TextStyle({ fontSize: 20, textAlign: TextAlign.center, fontWeight: FontWeight.w300 })) }),
            ],
            rows: []
        });
    }
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
    initState: function () {
        var jwt = createJWT({ "servidor": servidorGlobal, "idUsuario": idUsuario });
        _cargando = true;
        var response = http.get({ url: rutaGlobal + "/api/grupos/get?token=" + jwt, headers: Utils.headers }).then(function (json) {
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
    builder: function (id, setStateFromBuilder) {
        setState2 = setStateFromBuilder;
        console.log("Inside grupos");
        return Init({
            id: id,
            initDefaultStyle: true,
            child: 
            // Container({child: Texto("Holaaa soy un tipo vacacino")})
            _ventana({ editar: _mostrarVentanaEditar, grupo: _grupo })
        });
    }
});
