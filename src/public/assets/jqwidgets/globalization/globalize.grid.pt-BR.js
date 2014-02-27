function getGridLocalization() {

    var gridlocalization = {
        "/": "/",
        ":": ":",
        firstDay: 0,
        days: {
            names: ["Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"],
            namesAbbr: ["Dom", "Seg", "Ter", "Qua", "Qui", "Sex", "Sab"],
            namesShort: ["D", "S", "T", "Q", "U", "S", "S"]
        },
        months: {
            names: ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro" , ""],
            namesAbbr: ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez" , ""]
        },
        AM: null,
        PM: null,
        eras: [{"name":"d.C.","start":null,"offset":0}],
        twoDigitYearMax: 2029,
        patterns: {
            d: "dd/MM/yyyy",
            D: "dddd, d' de 'MMMM' de 'yyyy",
            t: "HH:mm",
            T: "HH:mm:ss",
            f: "dddd, d' de 'MMMM' de 'yyyy HH:mm",
            F: "dddd, d' de 'MMMM' de 'yyyy HH:mm:ss",
            M: "dd' de 'MMMM",
            Y: "MMMM' de 'yyyy"
        },
        percentsymbol: "%",
        currencysymbol: "R$",
        currencysymbolposition: "before",
        decimalseparator: ",",
        thousandsseparator: ".",
        pagergotopagestring: "Ir para página:",
        pagershowrowsstring: "Mostrar linhas:",
        pagerrangestring: " de ",
        pagerpreviousbuttonstring: "anterior",
        pagernextbuttonstring: "próxima",
        groupsheaderstring: "Arrante uma coluna e solte-a aqui para agrupar por esta coluna",
        sortascendingstring: "Ordem Crescente",
        sortdescendingstring: "Ordem Decrescente",
        sortremovestring: "Remover Ordenação",
        groupbystring: "Agrupar Por esta coluna",
        groupremovestring: "Remover dos grupos",
        filterclearstring: "Limpar",
        filterstring: "Filtrar",
        filtershowrowstring: "Mostrar linhas onde:",
        filterorconditionstring: "Ou",
        filterandconditionstring: "E",
        filterselectallstring: "(Selecionar Tudo)",
        filterchoosestring: "(Escolha)",
        filterstringcomparisonoperators: ["vazio", "não vazio", "contém", "contém (diferenciar maiúsculas)", "não contém", "não contém (diferenciar maiúsculas)", "comece com", "comece com (diferenciar maiúsculas)", "termine com", "termine com (diferenciar maiúsculas)", "igual", "igual (diferenciar maiúsculas)", "nulo", "não nulo"],
        filternumericcomparisonoperators: ["igual", "diferente", "menor que", "menor que ou igual", "maior que", "maior que ou igual", "nulo", "não nulo"],
        filterdatecomparisonoperators: ["igual", "diferente", "menor que", "menor que ou igual", "maior que", "maior que ou igual", "nulo", "não nulo"],
        filterbooleancomparisonoperators: ["igual", "diferente"],
        validationstring: "Valor informado é inválido",
        emptydatastring: "Sem dados para exibir",
        filterselectstring: "Select Filter",
        loadtext: "Carregando...",
        clearstring: "Limpar",
        todaystring: "Hoje"
    };

    return gridlocalization;
}