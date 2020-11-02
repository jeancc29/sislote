var setState2;
Builder({
    id: "containerWizard",
    builder: function (id, setStateFromBuilder) {
        setState2 = setStateFromBuilder;
        console.log("Inside grupos");
        return Init({
            id: id,
            child: 
            // Container({child: Texto("Holaaa soy un tipo vacacino")})
            Container({
                style: new TextStyle({ padding: EdgetInsets.only({ top: 20 }) }),
                child: DataTable({
                    columns: [
                        new DataColumn({ label: Texto("index", new TextStyle({ fontSize: 20, textAlign: TextAlign.center })) }),
                        new DataColumn({ label: Texto("Descripcion", new TextStyle({ fontSize: 20, textAlign: TextAlign.center })) }),
                        new DataColumn({ label: Texto("Estado", new TextStyle({ fontSize: 20, textAlign: TextAlign.center })) }),
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
            })
        });
    }
});
