import React, {useEffect} from 'react';
import Layout from '@/Shared/Layout';
import MaterialTable, {MTableToolbar} from '@material-table/core';
import CheckCircleIcon from '@material-ui/icons/CheckCircle';
import PropTypes from 'prop-types';
import {
    Grid, Typography, Stepper,
    Step, StepLabel, Button,
    Dialog, AppBar, Toolbar,
    IconButton, Slide, DialogContent,
    DialogContentText, FormControl, TextField,
    DialogTitle, DialogActions,
    Box, Tabs, Tab, Paper,
    Backdrop,
    CircularProgress
} from "@material-ui/core";

import materialTableLocaleES from "../../../Shared/MaterialTableLocateES";
import Card from "../../../Shared/DashboardComponents/Card/Card";
import CardHeader from "../../../Shared/DashboardComponents/Card/CardHeader";
import CardBody from "../../../Shared/DashboardComponents/Card/CardBody";
import CardFooter from "../../../Shared/DashboardComponents/Card/CardFooter";
import FileInput from '@/Shared/FileInput';
import {
    uploadExcelWamas,
    updateStops,
    calculateTimesByWave,
    caclulateBonus,
    getSorterBonus,
    getReportSorter,
    getPendingProds,
    processProd,
    getSorterStaffBonus,
    getReportStaffSorter,
} from "../../../Api/CheckinService/CheckinApi";
import { makeStyles, Theme, createStyles } from '@material-ui/core/styles';
import CloseIcon from '@material-ui/icons/Close';
import Moment from "moment";
import MomentUtils from "@date-io/moment";
import WeekPicker from "../../../Shared/weekPicker";
import { MuiPickersUtilsProvider } from "@material-ui/pickers";
Moment.locale("es");
Moment.weekdays(true);

const useStyles = makeStyles((theme) =>
  createStyles({
    root: {
      width: '100%',
    },
    backButton: {
      marginRight: theme.spacing(1),
    },
    instructions: {
      marginTop: theme.spacing(1),
      marginBottom: theme.spacing(1),
    },
    appBar: {
      position: 'relative',
    },
    title: {
      marginLeft: theme.spacing(2),
      flex: 1,
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
    pageRoot: {
      flexGrow: 1,
      padding: '20px',
      minWidth:600
    },
    backdrop: {
      zIndex: theme.zIndex.drawer + 1,
      color: '#fff',
    },
  }),
);

TabPanel.propTypes = {
    children: PropTypes.node,
    index: PropTypes.number.isRequired,
    value: PropTypes.number.isRequired,
};

function TabPanel(props) {
    const { children, value, index, ...other } = props;

    return (
        <Typography
            component="div"
            role="tabpanel"
            hidden={value !== index}
            id={`scrollable-auto-tabpanel-${index}`}
            aria-labelledby={`scrollable-auto-tab-${index}`}
            {...other}
        >
            {value === index && <Box p={3}>{children}</Box>}
        </Typography>
    );
}


function getSteps() {
  return ['Ingresa la productividad de WAMAs', 'Paros de ola(s)', 'Resta de tiempo a asociados'];
}

const  Transition = React.forwardRef(function Transition(props, ref) {
  return <Slide direction="up" ref={ref} {...props} />;
});

const Index = () => {
  const [fileWamas, setFileWamas] = React.useState();
  const [disabledNext, setDisabledNext] = React.useState(true);
  const classes = useStyles();
  const [activeStep, setActiveStep] = React.useState(0);
  const [waves, setWaves] = React.useState([]);
  const [associates, setAssociates] = React.useState([]);
  const steps = getSteps();
  const [open, setOpen] = React.useState(false);
  const [openCalculate, setOpenCalculate] = React.useState(false);
  const tableRefWaves = React.useRef();
  const tableRefAssociates = React.useRef();
  const tableRefPending = React.useRef();
  const [selectedDateInit, setSelectedDateInit] = React.useState(Moment().day("wednesday").subtract(7, 'days').format('YYYY-MM-DD'));
  const [selectedDateEnd, setSelectedDateEnd] = React.useState(Moment().format('YYYY-MM-DD'));
  const [data, setData] = React.useState([]);
  const [dataStaff, setDataStaff] = React.useState([]);
  const [pendingProductivity , setPendingProductivity] = React.useState([]);
  const [openQuestion, setOpenQuestion] = React.useState(false);
  const [dayProd, setDayProd] = React.useState('');
  const [tabValue, setTabValue] = React.useState(0);
  const [selectedDate, setSelectedDate] = React.useState(Moment().day("wednesday").format('YYYY-MM-DD'));
  const datePickerRef = React.useRef();
  const [openProgress, setOpenProgress] = React.useState(false);

  const handleChangeTabTwo = (event, newValue) => {
    setTabValue(newValue);
  };
  function a11yProps(index) {
    return {
      id: `scrollable-auto-tab-${index}`,
        'aria-controls': `scrollable-auto-tabpanel-${index}`,
    };
  }

  useEffect(() => {
    let filters = {
      dateInit : selectedDateInit,
      dateEnd : selectedDateEnd,
    };
    getSorterBonus(filters)
      .then(response => {
          setData(response.data);
      })
      .catch(error => {
            console.log(error)
      });
    getSorterStaffBonus( Moment(selectedDate).format('YYYY-MM-DD'))
        .then(response => {
            setDataStaff(response.data);
        })
        .catch(error => {
            console.log(error)
        });
  }, []);

  const handleNext = () => {
    setActiveStep((prevActiveStep) => prevActiveStep + 1);
  };
  const handleBack = () => {
    setActiveStep((prevActiveStep) => prevActiveStep - 1);
  };

  function handleSubmit(e) {
    e.preventDefault();
  }

  function getStepContent(stepIndex) {
    switch (stepIndex) {
      case 0:
        return (<form name="createForm" onSubmit={handleSubmit}>
          <Grid
            container
            spacing={2}
            direction="column"
            alignItems="center"
            justifyContent="center"
          >
            <Grid item xs={12}>
              <FileInput
                className="flex flex-wrap p-12 -mb-12 -mr-8"
                label="Archivo de WAMAs"
                name="excel"
                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
                value={fileWamas}
                onChange={ f => {
                    setFileWamas(f);
                    readExcelSku(f);
                }}
              />
            </Grid>
          </Grid>

        </form>);
      case 1:
        return (
          <MaterialTable
            columns={[
              { title: 'Ola', field: 'wave', editable: 'never' },
              { title: 'Paros', field: 'stops', type: 'numeric' },
            ]}
            data={waves}
            tableRef={tableRefWaves}
            title="Olas"
            localization={materialTableLocaleES}
            options={{
                search: false,
                showTitle: true,
                actionsColumnIndex: -1,
                padding: 'dense'
            }}
            editable={{
              onRowUpdate: (newData, oldData) =>
                new Promise((resolve, reject) => {
                  setTimeout(() => {
                    const dataUpdate = [...waves];
                    const index = oldData.tableData.id;
                    updateStops(newData, 'waves')
                      .then(response => {
                        if (response.message === 'ok') {
                          dataUpdate[index] = newData;
                          setWaves([...dataUpdate]);
                          resolve();
                        }
                      })
                      .catch(error => {
                        reject();
                        console.log(error)
                      });
                  }, 1000)
                }),
            }}
          />
        );
      case 2:
        return (
          <MaterialTable
            columns={[
              { title: 'Nombre', field: 'name', editable: 'never' },
              { title: 'Usuario', field: 'user', editable: 'never' },
              { title: 'Ola', field: 'wave', editable: 'never' },
              { title: 'Piezas', field: 'pieces', editable: 'never' },
              { title: 'Prepacks', field: 'ppk', editable: 'never' },
              { title: 'Inducciones', field: 'inductions', editable: 'never' },
              { title: 'Horas de induccion', field: 'active_time', editable: 'never' },
              { title: 'Paros', field: 'stops', type: 'numeric' },
            ]}
            data={associates}
            tableRef={tableRefAssociates}
            title="Asociados"
            localization={materialTableLocaleES}
            options={{
              search: false,
              showTitle: true,
              actionsColumnIndex: -1,
              pageSize: 10,
              padding: 'dense',
            }}
            editable={{
              onRowUpdate: (newData, oldData) =>
                new Promise((resolve, reject) => {
                  setTimeout(() => {
                    const dataUpdate = [...associates];
                    const index = oldData.tableData.id;
                    updateStops(newData, 'associates')
                      .then(response => {
                        if (response.message === 'ok') {
                          dataUpdate[index] = newData;
                          setAssociates([...dataUpdate]);
                          resolve();
                        }
                      })
                      .catch(error => {
                        reject();
                        console.log(error)
                      });
                  }, 1000)
                }),
            }}
          />
        );
      default:
        return handleClose();
    }
  }

  const readExcelSku = (file) => {
    if (file != null) {
      setOpenProgress(!openProgress);
      const formData = new FormData();
      formData.append('file',file);
      const config = {
        headers: { 'Content-Type': 'multipart/form-data'}
      };
      uploadExcelWamas(formData, config)
        .then(response => {
          setWaves(response.data.waves);
          setAssociates(response.data.associates);
          setDisabledNext(false);
          setOpenProgress(false);
        })
        .catch(error => {
            console.log(error)
        });
    }
  };

  function calculateTimes() {
      console.log(waves);
    calculateTimesByWave(waves)
      .then(response => {
        if (response.message === 'ok') {
          setAssociates([]);
          setWaves([]);
          setActiveStep(0);
          setOpen(false);
          setDisabledNext(true);
          caclulateBonus()
        }
      })
      .catch(error => {
        console.log(error)
      });
  }

  function runProductivity() {
    processProd(dayProd)
      .then(response => {
        getPendingProds()
          .then(response => {
            let prods = response.productivity;
            if (prods.length > 0) {
              tableRefPending.current.onQueryChange();
              setOpenQuestion(false);
            } else {
              setOpenQuestion(false);
              setOpenCalculate(false);
              tableRefPending.current.onQueryChange();
             }
          })
          .catch(error => {
            console.log(error)
          });
    })
    .catch(error => {
      console.log(error)
    });
  }

  const handleClickOpen = () => {
    setOpen(true);
  };

  const handleCloseAlert = () => {
    setOpenQuestion(false);
  };

  const handleClickOpenCalculate = () => {

    getPendingProds()
      .then(response => {
          setPendingProductivity(response.productivity);
      })
      .catch(error => {
          console.log(error)
      });
    setOpenCalculate(true);
  };

  const handleClose = () => {
    setAssociates([]);
    setWaves([]);
    setActiveStep(0);
    setOpen(false);
    setDisabledNext(true);
  };

  const handleCloseCalculate = () => {
    setOpenCalculate(false);
  };

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

  function refreshSorterBonusTable() {
    let filters = {
      dateInit : selectedDateInit,
      dateEnd : selectedDateEnd,
    };
    getSorterBonus(filters)
      .then(response => {
        setData(response.data);
      })
      .catch(error => {
        console.log(error)
      });
  }

  function runProd(data) {
      setOpenQuestion(true);
      setDayProd(data);
  }

  const getPending = (query) =>
    getPendingProds()
      .then(response => {
        return {
          ...query,
          page: 0,
          pageSize: 10,
          totalCount: response.productivity.length,
          data: response.productivity,
        };
      })
      .catch(() => ({
        ...query,
        page: 0,
        pageSize: 15,
        totalCount: 0,
        data: [],
      }));

  function refreshStaffTable() {
    let date1 = Moment(datePickerRef.current.state.selectedDate._d).format('YYYY-MM-DD');
    setSelectedDate(Moment(datePickerRef.current.state.selectedDate._d).format('YYYY-MM-DD'));
    getSorterStaffBonus(date1)
      .then(response => {
          setDataStaff(response.data);
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
    getReportSorter(filters)
        .then( response  => {
            const contentDisposition = response.headers['content-disposition'];
            const contentType = response.headers['content-type'];
            const filename = 'productividad_sorter.xlsx';
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

  const getReportStaff = () => {
    let filters = {
        dateInit : selectedDate,
        dateEnd : Moment(datePickerRef.current.state.selectedDate._d).add(6, 'days').format('YYYY-MM-DD'),
    };
    getReportStaffSorter(filters)
      .then( response  => {
        const contentDisposition = response.headers['content-disposition'];
        const contentType = response.headers['content-type'];
        const filename = 'productividad-sorter-staff.xlsx';
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
      <h1 className="mb-8 text-3xl font-bold">Productividad Sorter</h1>
      <Paper className={classes.pageRoot}>
          <AppBar
            position="static"
            color="default"
            style={{ alignItems: 'center', backgroundColor: 'white' , marginBottom: '20px'}}
          >
            <Tabs
              value={tabValue}
              onChange={handleChangeTabTwo}
              indicatorColor="primary"
              textColor="primary"
              variant="scrollable"
              scrollButtons="auto"
              aria-label="Elija el area"
            >
              <Tab label="INDUCCION" {...a11yProps(0)} />
              <Tab label="STAFF/MANAGERS" {...a11yProps(1)} />
            </Tabs>
          </AppBar>

          <TabPanel value={tabValue} index={0}>

          <div className="flex items-center justify-between mb-6">
            <Button className="btn-indigo focus:outline-none" variant="contained" color="primary" onClick={handleClickOpen}>
              <span className="hidden md:inline"> Procesar de WAMAs</span>
            </Button>
            <Button className="btn-indigo focus:outline-none" variant="contained" color="secondary" onClick={handleClickOpenCalculate}>
              <span className="hidden md:inline"> Calcular Productividad</span>
            </Button>
          </div>

          <MaterialTable
            columns={[
              { title: 'Dia', field: 'bonus_date' },
              { title: 'Nombre', field: 'name' },
              { title: 'Usuario', field: 'wamas_user' },
              { title: 'Area', field: 'area' },
              { title: 'Prepacks', field: 'ppk_shift' },
              { title: 'Bono', field: 'bonus_amount' },
            ]}
            data={data}
            title="Asociados"
            localization={materialTableLocaleES}
            options={{
              search: false,
              showTitle: false,
              actionsColumnIndex: -1,
              pageSize: 20,
              padding: 'dense',
              exportMenu: [
                {
                  label: 'Excel',
                  exportFunc: () => {getReport()}
                }
              ],
            }}
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
                      <Button variant="contained" color="primary" size="small" onClick={refreshSorterBonusTable}>
                        Filtrar
                      </Button>
                    </FormControl>
                </div>
              ),
            }}
          />
          <Dialog fullScreen open={open} onClose={handleClose} TransitionComponent={Transition}>
            <AppBar className={classes.appBar}>
              <Toolbar>
                <IconButton edge="start" color="inherit" onClick={handleClose} aria-label="close">
                    <CloseIcon />
                </IconButton>
                  <Backdrop className={classes.backdrop} open={openProgress}>
                      <CircularProgress color="inherit" />
                  </Backdrop>
                <Typography variant="h6" className={classes.title}>
                    INGRESO DE PRODUCTIVIDAD SORTER
                </Typography>
                <Button autoFocus color="inherit" onClick={handleClose}>
                    CANCELAR
                </Button>
              </Toolbar>
            </AppBar>
            {activeStep === steps.length ? (
              <div>
                <Grid container spacing={1} direction="row" justifyContent="center" alignItems="baseline">
                  <Grid item xs={12}>
                    <Card style={{height:'100%'}}>
                      <CardHeader>
                        <h1 className="mb-8 text-3xl font-bold">Se completo el registro de productividad</h1>
                      </CardHeader>
                      <CardBody>
                        <Button onClick={calculateTimes} className={classes.backButton}>
                          CERRAR
                        </Button>
                      </CardBody>
                    </Card>
                  </Grid>
                </Grid>
              </div>
            ) : (
              <div>
                <Grid container spacing={1} direction="row" justifyContent="center" alignItems="baseline">
                  <Grid item xs={12}>
                    <Card style={{height:'100%'}}>
                      <CardHeader>
                        <Stepper activeStep={activeStep} alternativeLabel>
                          {steps.map((label) => (
                            <Step key={label}>
                              <StepLabel>{label}</StepLabel>
                            </Step>
                          ))}
                        </Stepper>
                      </CardHeader>
                      <CardBody>
                        {getStepContent(activeStep)}
                      </CardBody>
                      <CardFooter>
                        <div>
                          <Button
                            disabled={activeStep === 0}
                            onClick={handleBack}
                            className={classes.backButton}
                          >
                            Atras
                          </Button>
                          <Button variant="contained" color="primary" onClick={handleNext} disabled={disabledNext}>
                            {activeStep === steps.length - 1 ? 'Termino' : 'Siguiente'}
                          </Button>
                        </div>
                      </CardFooter>
                    </Card>
                  </Grid>
                </Grid>
              </div>
              )}
          </Dialog>
          <Dialog fullScreen open={openCalculate} onClose={handleCloseCalculate} TransitionComponent={Transition}>
            <AppBar className={classes.appBar}>
              <Toolbar>
                <IconButton edge="start" color="inherit" onClick={handleCloseCalculate} aria-label="close">
                  <CloseIcon />
                </IconButton>
                <Typography variant="h6" className={classes.title}>
                  CALCULAR PRODUCTIVIDADES INGRESADAS
                </Typography>
                <Button autoFocus color="inherit" onClick={handleCloseCalculate}>
                  CANCELAR
                </Button>
              </Toolbar>
            </AppBar>
            <MaterialTable
              columns={[
                { title: 'Día', field: 'day', editable: 'never' },
                { title: 'Número de olas', field: 'count', editable: 'never' },
                { title: 'Olas Registradas', field: 'wave', editable: 'never' },
              ]}
              data={query => getPending(query)}
              tableRef={tableRefPending}
              title="Asociados"
              localization={materialTableLocaleES}
              options={{
                search: false,
                showTitle: true,
                actionsColumnIndex: -1,
                pageSize: 10,
                padding: 'dense',
              }}
              actions={[
                {
                  icon: (rowData) => (
                    <CheckCircleIcon color='primary' className="icon-small" />
                  ),
                  tooltip: 'Calcular',
                  onClick: (event, rowData) => {
                    runProd(rowData.day)
                  }
                },
              ]}
            />
          </Dialog>
          <Dialog
            open={openQuestion}
            onClose={handleCloseAlert}
            aria-labelledby="alert-dialog-title"
            aria-describedby="alert-dialog-description"
          >
            <DialogTitle id="alert-dialog-title">
                {"PROCESAR PRODUCTIVIDAD"}
            </DialogTitle>
            <DialogContent>
              <DialogContentText id="alert-dialog-description">
                Esta de acuerdo en procesar la productividad del dia: {dayProd}
              </DialogContentText>
            </DialogContent>
            <DialogActions>
              <Button onClick={handleCloseAlert}>Cancelar</Button>
              <Button onClick={runProductivity} autoFocus>
                Procesar
              </Button>
            </DialogActions>
          </Dialog>
          </TabPanel>
          <TabPanel value={tabValue} index={1}>
            <MaterialTable
              columns={[
                { title: 'Nombre', field: 'name' },
                { title: 'Usuario', field: 'wamas_user' },
                { title: 'Area', field: 'area' },
                { title: 'Subarea', field: 'subarea' },
                { title: 'Bono', field: 'bonus_amount' },
              ]}
              data={dataStaff}
              title="Asociados"
              localization={materialTableLocaleES}
              options={{
                search: false,
                showTitle: false,
                actionsColumnIndex: -1,
                pageSize: 20,
                padding: 'dense',
                exportMenu: [
                  {
                    label: 'Excel',
                    exportFunc: () => {getReportStaff()}
                  }
                ],
              }}
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
                        <Button variant="contained" color="primary" size="small" onClick={refreshStaffTable}>
                          Filtrar
                        </Button>
                      </FormControl>
                  </div>
                ),
              }}
            />
          </TabPanel>
      </Paper>
    </div>
  );
};

Index.layout = page => <Layout title="Sorter" children={page} />;

export default Index;