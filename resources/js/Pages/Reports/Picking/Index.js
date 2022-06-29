import React, { useEffect, useState, useRef }  from 'react';
import Layout from '@/Shared/Layout';
import FileInput from '@/Shared/FileInput';
import MaterialTable, {MTableToolbar} from '@material-table/core';
import { MuiPickersUtilsProvider } from "@material-ui/pickers";
import { Inertia } from '@inertiajs/inertia';
import {FormControl, Grid, makeStyles, TextField, Typography} from "@material-ui/core";
import {InertiaLink, usePage} from "@inertiajs/inertia-react";
import {Delete, Visibility as ShowIcon, Visibility as ViewIcon} from '@material-ui/icons';
import Button from '@material-ui/core/Button';
import materialTableLocaleES from '../../../Shared/MaterialTableLocateES';
import WeekPicker from "../../../Shared/weekPicker";
import MomentUtils from "@date-io/moment";
import Moment from 'moment';

import * as XLSX from "xlsx";
import {loadPickingProductivity, getPickingBonus, getReportPicking} from "../../../Api/CheckinService/CheckinApi";

Moment.locale("es");

Moment.weekdays(true);
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
    minWidth: 160,
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
  const { bonus } = usePage().props;
  const tableRef = React.useRef();
  const datePickerRef = React.useRef();
  const [data, setData] = React.useState([]);  
  const [selectedDate, setSelectedDate] = React.useState(Moment().day("wednesday").format('YYYY-MM-DD'));
  const [fileInput, setFileInput]= useState('');
  const [loading, setLoading] = useState(false);
  const [fileInputName, setFileInputName]= useState('');

  useEffect(() => {
    let date1 = Moment(selectedDate).format('YYYY-MM-DD');
    let date2 = Moment(selectedDate).add(6, 'days').format('YYYY-MM-DD');
    let filters = {
        dateInit : date1,
        dateEnd : date2,
    };
    getPickingBonus(filters)
        .then(response => {
          setData(response.data);
        })
    .catch(error => {
            console.log(error)
        });
  }, []);

  const getReport = () => {
    let date1 = Moment(selectedDate).format('YYYY-MM-DD');
    let date2 = Moment(selectedDate).add(6, 'days').format('YYYY-MM-DD');    
    let filters = {
        dateInit : date1,
        dateEnd : date2,
    };
    setSelectedDate(Moment(selectedDate).format('YYYY-MM-DD'));
    getReportPicking(filters)
      .then( response  => {
          const contentDisposition = response.headers['content-disposition'];
          const contentType = response.headers['content-type'];
          const filename = 'productividad_picking.xlsx';
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

  // const handleSubmitFile = (event) => {    
  //   setFileInputName(event.target.file.name);
  // };

  const readExcel = (file) => {      
      const promise = new Promise((resolve, reject) => {
          const fileReader = new FileReader();
          fileReader.readAsArrayBuffer(file);
          fileReader.onload = (e) => {
              const bufferArray = e.target.result;
              const wb = XLSX.read(bufferArray, { type: "buffer" });
              const wsname = wb.SheetNames[0];
              const ws = wb.Sheets[wsname];
              const data = XLSX.utils.sheet_to_json(ws);
              resolve(data);
          };

          fileReader.onerror = (error) => {
              reject(error);
          };
      });
      promise.then((d) => {
          setLoading(prevLoading => !prevLoading);
          loadPickingProductivity(d)
              .then(response => {
                  setData(response);
                  return response;
              })
              .then(response => {
                  setLoading(prevLoading => !prevLoading);
              })
              .catch(e => {
                  console.log(e);
              });

      });
  };

  function refreshPickingBonusTable() {
    let date1 = Moment(datePickerRef.current.state.selectedDate._d).format('YYYY-MM-DD');
    let date2 = Moment(datePickerRef.current.state.selectedDate._d).add(6, 'days').format('YYYY-MM-DD');    
    let filters = {
        dateInit : date1,
        dateEnd : date2,
    };
    setSelectedDate(Moment(datePickerRef.current.state.selectedDate._d).format('YYYY-MM-DD'));
    getPickingBonus(filters)
        .then(response => {
            setData(response.data);
        })
        .catch(error => {
            console.log(error)
        });
  }

  return (
    <div>
      <h1 className="mb-8 text-3xl font-bold">Productividad en Picking</h1>
      <MaterialTable
        columns={[
          { title: 'No. Empleado', field: 'employee_number'},
          { title: 'Nombre', field: 'name'},
          {
            title: 'Fecha de prod.',
            field: 'bonus_date',
            render: rowData => {
              return  Moment(rowData.bonus_date).format("YYYY-MM-DD");
            },
          },
          {
            title: 'Cajas / turno',
            field: 'boxes_shift',
            type: 'numeric',
            render: rowData => {
              return  rowData.boxes_shift;
            },
          },
          {
            title: 'Bono',
            field: 'bonus_amount',
            type: 'currency',
            render: rowData => {
              return  '$ '+rowData.bonus_amount.toFixed(2);
            },
          },

        ]}
        options={{
          search: true,
          showTitle: false,
          padding: 'dense',
          pageSize: 20,
          exportMenu: [
            {
              label: 'Excel',
                exportFunc: () => {                  
                  getReport();
                }
              }
          ],
          actionsColumnIndex: -1,
        }}
        tableRef={tableRef}
        localization={materialTableLocaleES}
        data={data}        
        title="Productividad de picking"
        components={{
          Toolbar: props => (
            <div>
              <MTableToolbar {...props} />              
              <FormControl className={classes.formControlTableToolBar}>
                <MuiPickersUtilsProvider utils={MomentUtils} locale="es">
                  <WeekPicker ref={datePickerRef} selectedDate={selectedDate} />
                </MuiPickersUtilsProvider>
              </FormControl>
              <FormControl className={classes.formControlTableToolBarButton}>
                <Button variant="contained" color="primary" size="small" onClick={refreshPickingBonusTable}>
                  Buscar
                </Button>
              </FormControl>
              <FormControl className={classes.formControlTableToolBarButton} style={{marginTop: '-20px'}}>
                <FileInput
                  label="Cargar Excel"
                  name="excel"
                  accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"                  
                  value={fileInput}
                  onChange={(file) => {
                    //handleSubmitFile(file);
                    setFileInput(file);
                    readExcel(file);
                  }}
                />
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
