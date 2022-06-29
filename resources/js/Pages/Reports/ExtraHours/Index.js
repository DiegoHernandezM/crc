import React, { useEffect }  from 'react';
import Layout from '@/Shared/Layout';
import MaterialTable, {MTableToolbar} from '@material-table/core';
import { Inertia } from '@inertiajs/inertia';
import {FormControl, Grid, makeStyles, TextField} from "@material-ui/core";
import {InertiaLink, usePage} from "@inertiajs/inertia-react";
import {Delete, Visibility as ShowIcon, Visibility as ViewIcon} from '@material-ui/icons';
import Button from '@material-ui/core/Button';
import materialTableLocaleES from '../../../Shared/MaterialTableLocateES';
import moment from "moment";
import {getDataExtraHours, getExcelExtraHours, getReportExcel} from "../../../Api/CheckinService/CheckinApi";

const useStyles = makeStyles(theme => ({
  root: {},
  media: {
    height: 300,
  },
  formControlTableToolBar: {
    margin: theme.spacing(1),
    marginTop: -theme.spacing(1),
    marginRight: theme.spacing(2),
    minWidth: 160,
    maxWidth: 10,
  },
  formControlTableToolBarButton: {
    margin: theme.spacing(1),
    marginTop: -theme.spacing(-1),
    marginRight: theme.spacing(2),
    minWidth: 10,
    maxWidth: 10,
  },
  textField: {
    marginLeft: theme.spacing(1),
    marginRight: theme.spacing(1),
    width: 200,
  },
}));

const Index = () => {
  const classes = useStyles();
  const tableRef = React.useRef();
  const [data, setData] = React.useState([]);
  const [selectedDateInit, setSelectedDateInit] = React.useState(null);
  const [selectedDateEnd, setSelectedDateEnd] = React.useState(null);

  useEffect(() => {
    let filters = {
        dateInit : selectedDateInit,
        dateEnd : selectedDateEnd,
    };
    getDataExtraHours(filters)
        .then(response => {
          setData(response.data);
        })
    .catch(error => {
            console.log(error)
        });
  }, []);

  const handleChangeDateInit = event => {
    setSelectedDateInit(event.target.value);
  };
  const handleChangeDateEnd = event => {
    setSelectedDateEnd(event.target.value);
  };
  const InputComponentInit = ({ defaultValue, inputRef, ...props }) => {
    const classes = useStyles();
    const handleChange = event => {
      setSelectedDateInit(event.target.value);
      if (props.onChange) {
        props.onChange(event);
      }
    };
    return (
      <div className={classes.root}>
        <input
          className={classes.input}
          ref={inputRef}
          {...props}
          onChange={handleChange}
          value={selectedDateInit}
        />
      </div>
    );
  };
  const InputComponentEnd = ({ defaultValue, inputRef, ...props }) => {
    const classes = useStyles();
    const handleChange = event => {
      setSelectedDateEnd(event.target.value);
      if (props.onChange) {
        props.onChange(event);
      }
    };
    return (
      <div className={classes.root}>
        <input
          className={classes.input}
          ref={inputRef}
          {...props}
          onChange={handleChange}
          value={selectedDateEnd}
        />
      </div>
    );
  };

  const renderDetailCheckin = rowData => {
    return (
      <MaterialTable
        nameEntity="registros"
        columns={[
          {
            title: 'Dia',
            field: 'checkin',
            render: rowData => {
              return  moment(rowData.checkin).format("YYYY-MM-DD");
            },
          },
          {
            title: 'Entrada',
            field: 'checkin',
            render: rowData => {
              return  moment(rowData.checkin).format("HH:mm");
            },
          },
          {
            title: 'Salida',
            field: 'checkout',
            render: rowData => {
              return  moment(rowData.checkout).format("HH:mm");
            },
          },
          {
            title: 'Horas Extra',
            field: 'extra',
          },
          {
            title: 'Comentarios',
            field: 'comments',
          },
        ]}
        data={rowData.rowData.checkin}
        options={{
          search: false,
          showTitle: false,
          toolbar: false,
          paging: false,
          header: true,
          padding: 'dense',
          rowStyle: {
            backgroundColor: '#eeeeee',
          },
        }}
        />
    );
  };

  function getReportHours() {
    let filters = {
        dateInit : selectedDateInit,
        dateEnd : selectedDateEnd,
    };
    getDataExtraHours(filters)
        .then(response => {
            console.log(response.data);
            setData(response.data);
        })
        .catch(error => {
            console.log(error)
        });
  }

  const getReport = () => {
    let filters = {
        dateInit : selectedDateInit,
        dateEnd : selectedDateEnd,
    };
    return getExcelExtraHours(filters)
        .then( response  => {
            setSelectedDateInit(null);
            setSelectedDateEnd(null);
            const contentDisposition = response.headers['content-disposition'];
            const contentType = response.headers['content-type'];
            const filename = 'reporte-horas-extra.xlsx';
            const file = new Blob([response.data], { type: contentType });
            // For Internet Explorer and Edge
            if ('msSaveOrOpenBlob' in window.navigator) {
                window.navigator.msSaveOrOpenBlob(file, filename);
            }
            // For Firefox and Chrome
            else {
                // Bind blob on disk to ObjectURL
                const data = URL.createObjectURL(file);
                const a = document.createElement('a');
                a.style = 'display: none';
                a.href = data;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                // For Firefox
                setTimeout(function() {
                    document.body.removeChild(a);
                    // Release resource on disk after triggering the download
                    window.URL.revokeObjectURL(data);
                }, 100);
            }
        })
        .then(response => {
            return true;
        })
        .catch(error => {
            console.log(error)
        });
  };

  return (
    <div>
      <h1 className="mb-8 text-3xl font-bold">Horas Extra</h1>
      <MaterialTable
        columns={[
          { title: 'No. Empleado', field: 'employee_number'},
          { title: 'Nombre', field: 'name'},
          { title: 'Area', field: 'associate_type'},
          {
            title: 'Fecha de ingreso',
            field: 'entry_date',
            render: rowData => {
              return  moment(rowData.entry_date).format("YYYY-MM-DD");
            },
          },

        ]}
        options={{
          search: true,
          showTitle: false,
          exportMenu: [
            {
              label: 'Excel',
                exportFunc: () => {getReport()}
              }
          ],
          pageSize: 10,
          padding: 'dense',
          actionsColumnIndex: -1,
        }}
        tableRef={tableRef}
        localization={materialTableLocaleES}
        data={data}
        detailPanel={rowData => renderDetailCheckin(rowData)}
        title="Horas Extra"
        components={{
          Toolbar: props => (
            <div>
              <MTableToolbar {...props} />
              <FormControl className={classes.formControlTableToolBar}>
                <TextField
                  fullWidth
                  id="date-Init"
                  label="Fecha Inicio"
                  type="date"
                  onChange={handleChangeDateInit}
                  InputProps={{
                    inputComponent: InputComponentInit,
                  }}
                  defaultValue={selectedDateInit}
                  InputLabelProps={{
                    shrink: true,
                  }}
                />
              </FormControl>
              <FormControl className={classes.formControlTableToolBar}>
                <TextField
                  fullWidth
                  id="date-End"
                  label="Fecha Fin"
                  type="date"
                  onChange={handleChangeDateEnd}
                  InputProps={{
                    inputComponent: InputComponentEnd,
                  }}
                  defaultValue="2017-05-24"
                  InputLabelProps={{
                    shrink: true,
                  }}
                />
              </FormControl>
              <FormControl className={classes.formControlTableToolBarButton}>
                <Button variant="contained" color="primary" size="small" onClick={getReportHours}>
                  Filtrar
                </Button>
              </FormControl>
            </div>
          ),
        }}
        />
    </div>
  );
};

Index.layout = page => <Layout title="Tablero" children={page} />;

export default Index;
