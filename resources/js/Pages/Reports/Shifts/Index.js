import React from 'react';
import Layout from '@/Shared/Layout';

// Api
import {getAssistences, validateUserPassword, updateCheckin, newCheckin, getReportExcel, getReportGeneralExcel} from '../../../Api/CheckinService/CheckinApi';

// Components
import {
    Card, CardContent, Grid, Typography, Dialog,
    DialogActions, DialogContent, DialogTitle, makeStyles,
    TextField, CardMedia, Button, FormControl, DialogContentText
} from '@material-ui/core';
import materialTableLocaleES from "../../../Shared/MaterialTableLocateES";
import MaterialTable, { MTableToolbar } from "@material-table/core";
import {InertiaLink, usePage} from "@inertiajs/inertia-react";
import {
    Visibility as ViewIcon,
} from '@material-ui/icons';
import moment from "moment";
import axios from 'axios';

const useStyles = makeStyles(theme => ({
    root: {
    },
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
    textField: {
      marginLeft: theme.spacing(1),
      marginRight: theme.spacing(1),
      width: 200,
    },
}));

const Index = () => {
  const classes = useStyles();
  const { associates } = usePage().props;
  const tableRef = React.useRef();
  const [area, setArea] = React.useState('');
  const [shiftA, setShiftA] = React.useState('');
  const [openDetail, setOpenDetail] = React.useState(false);
  const [openPassword, setOpenPassword] = React.useState(false);
  const [openExport, setOpenExport] = React.useState(false);
  const [openExportGeneral, setOpenExportGeneral] = React.useState(false);
  const [dataAssociate, setDataAssociate] = React.useState([]);
  const [dataRegister, setDataRegister] = React.useState([]);
  const [messagePassword, setMessagePassword] = React.useState('');
  const [password, setPassword] = React.useState('');
  const [selectedDateInit, setSelectedDateInit] = React.useState(null);
  const [selectedDateInitGeneral, setSelectedDateInitGeneral] = React.useState(null);
  const [selectedDateEnd, setSelectedDateEnd] = React.useState(null);
  const [selectedDateEndGeneral, setSelectedDateEndGeneral] = React.useState(null);
  const [selectedDateInitExcel, setSelectedDateInitExcel] = React.useState(null);
  const [selectedDateInitExcelGeneral, setSelectedDateInitExcelGeneral] = React.useState(null);
  const [selectedDateEndExcel, setSelectedDateEndExcel] = React.useState(null);
  const [selectedDateEndExcelGeneral, setSelectedDateEndExcelGeneral] = React.useState(null);
  const [action, setAction] = React.useState('');
  const [comments, setComments] = React.useState('');
  const [validateComments, setValidateComments] = React.useState('');
  const handleClose = () => {
    setSelectedDateEnd(null);
    setSelectedDateInit(null);
    setOpenDetail(false);
  };
  const handleClosePassword = () => {
    setOpenPassword(false);
    setPassword('');
    setMessagePassword('');
    setComments('');
    setValidateComments('');
    tableRef.current.onQueryChange();
  };
  const handleCloseExport = () => {
    setOpenExport(false);
  };
  const handleCloseExportGeneral = () => {
    setOpenExportGeneral(false);
  };
  const handleChangeDateInit = event => {
    if (tableRef.current) {
      tableRef.current.state.query.page = 0;
      tableRef.current.state.query.dateInit = event.target.value;
      tableRef.current.state.query.dateEnd = selectedDateEnd;
      setSelectedDateInit(event.target.value);
      tableRef.current.onQueryChange();
    }
  };
  const handleChangeDateEnd = event => {
    if (tableRef.current) {
      tableRef.current.state.query.page = 0;
      tableRef.current.state.query.dateEnd = event.target.value;
      tableRef.current.state.query.dateInit = selectedDateInit;
      setSelectedDateEnd(event.target.value);
      tableRef.current.onQueryChange();
    }
  };
  const InputComponentInitExcel = ({ defaultValue, inputRef, ...props }) => {
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
  const InputComponentInitExcelGeneral = ({ defaultValue, inputRef, ...props }) => {
    const classes = useStyles();
    const handleChange = event => {
        setSelectedDateInitGeneral(event.target.value);
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
                value={selectedDateInitGeneral}
            />
        </div>
    );
  };
  const InputComponentEndExcel = ({ defaultValue, inputRef, ...props }) => {
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
  const InputComponentEndExcelGeneral = ({ defaultValue, inputRef, ...props }) => {
    const classes = useStyles();
    const handleChange = event => {
        setSelectedDateEndGeneral(event.target.value);
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
                value={selectedDateEndGeneral}
            />
        </div>
    );
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
  const validatePassword = () => {
      if (comments !== '') {
          return validateUserPassword(password)
              .then(response => {
                  if (response.message === 'ok') {
                      setPassword('');
                      setMessagePassword('');
                      setOpenPassword(false);
                      setComments('');
                      setValidateComments('');
                      setValidateComments('');
                      if (action === 'update') {
                          if (updateRegister(dataRegister)) {
                              tableRef.current.onQueryChange();
                          }
                      }
                      if (action === 'new')  {
                          newRegister(dataRegister);
                          tableRef.current.onQueryChange();
                      }
                  } else {
                      setMessagePassword(response.message);
                  }
              });
      } else {
          setValidateComments('Los comentarios son requeridos');
      }

  };

  const getAllAssistences = (associate, query) =>
    getAssistences(
      associate, query.page + 1, query.pageSize, selectedDateInit, selectedDateEnd
    )
      .then(response => ({
        ...query,
        data: response.assistences.data,
        page: response.assistences.current_page - 1,
        totalCount: response.assistences.total,
      }))
      .catch(error => {
        console.log(error)
      });
  const updateRegister = (data) =>
    updateCheckin(data , comments)
      .then(response => {
        if (response.message === 'ok') {
          return true;
        } else {
          return false;
        }
      })
      .catch(error => {
        console.log(error)
      });
  const newRegister = (data) => {
      let dataCheckin = {
            id : dataAssociate.id,
            created_at : data.created_at,
            checkin : data.checkin,
            checkout : data.checkout,
      };
      return newCheckin(dataCheckin)
          .then(response => {
            return true;
          })
          .catch(error => {
              console.log(error)
          });
  };
  const getReport = () => {
    let filters = {
      id : dataAssociate.id,
      dateInit : selectedDateInitExcel,
      dateEnd : selectedDateEndExcel,
    };
    return getReportExcel(filters)
      .then( response  => {
        setSelectedDateInitExcel(null);
        setSelectedDateEndExcel(null);
        setSelectedDateInit(null);
        setSelectedDateEnd(null);
        const contentDisposition = response.headers['content-disposition'];
        const contentType = response.headers['content-type'];
        const filename = dataAssociate.employee_number+'-asistencias.xlsx';
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
        tableRef.current.onQueryChange();
        setOpenExport(false);
        setOpenDetail(false);
      })
      .then(response => {
        return true;
      })
      .catch(error => {
        console.log(error)
      });
  };

  const getReportGeneral = () => {
    let filters = {
        dateInit : selectedDateInitExcelGeneral,
        dateEnd : selectedDateEndExcelGeneral,
    };
    return getReportGeneralExcel(filters)
        .then( response  => {
            setSelectedDateInitExcelGeneral(null);
            setSelectedDateEndExcelGeneral(null);
            setSelectedDateInitGeneral(null);
            setSelectedDateEndGeneral(null);
            const contentDisposition = response.headers['content-disposition'];
            const contentType = response.headers['content-type'];
            const filename = 'reporte-asistencias.xlsx';
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
            tableRef.current.onQueryChange();
            setOpenExportGeneral(false);
        })
        .then(response => {
            return true;
        })
        .catch(error => {
            console.log(error)
        });
  };

  return (
    <div className={classes.root}>
      <h1 className="mb-8 text-3xl font-bold">Reporte de asistencia</h1>
        <Grid>
          <Grid item xs={12} md={12}>
            <MaterialTable
              columns={[
                {
                  title: '',
                  field: 'picture',
                  render: rowData => {
                    let photo = (rowData.picture != null) ? rowData.picture : '/person.png';
                    return <img
                      style={{ height: 36, borderRadius: '50%' }}
                      src={photo}
                    />
                  }
                },
                { title: 'No.Empleado', field: 'employee_number' },
                { title: 'Nombre', field: 'name' },
              ]}
              localization={materialTableLocaleES}
              options={{
                pageSize: 10,
                search: true,
                padding: 'dense',
                actionsColumnIndex: -1,
                pageSizeOptions: [5, 10, 20, 50, 100],
                exportMenu: [
                  {
                      label: 'Reporte general',
                      exportFunc: () => {setOpenExportGeneral(true)}
                  }
                ],
              }}
              data={associates}
              title="Asociados"
              actions={[
                {
                  icon: () => <ViewIcon color="primary" className="icon-small" />,
                  tooltip: 'Detalle',
                  onClick: (event, rowData) => {
                      setArea(rowData.area.name);
                      setShiftA(rowData.shift.name);
                      setDataAssociate(rowData);
                      setOpenDetail(!openDetail);
                  },
                }
              ]}
            />
          </Grid>
        </Grid>
        <Dialog open={openDetail} onClose={handleClose} aria-labelledby="form-dialog-title" fullWidth maxWidth="xl">
          <DialogTitle id="form-dialog-title">Detalle de asistencias</DialogTitle>
            <DialogContent>
              <Grid container spacing={3}>
                <Grid item xs={12} md={2}>
                  <Card className={classes.root}>
                    <CardMedia
                      className={classes.media}
                      image={(dataAssociate.picture != null) ? dataAssociate.picture : '/person.png'}
                      title={dataAssociate.picture}
                    />
                    <CardContent>
                      <Typography variant="h6" gutterBottom>
                          No. Empleado: {dataAssociate.employee_number}
                      </Typography>
                      <Typography variant="h6" gutterBottom>
                          Nombre: {dataAssociate.name}
                      </Typography>
                      <Typography variant="h6" gutterBottom>
                          Area: {area}
                      </Typography>
                      <Typography variant="h6" gutterBottom>
                          Horario: {shiftA}
                      </Typography>
                    </CardContent>
                  </Card>
                </Grid>
                <Grid item xs={12} md={10}>
                  <MaterialTable
                    columns={[
                      { title: 'Día',
                        field: 'checkin',
                        render: rowData => {
                            return  moment(rowData.checkin).format("YYYY-MM-DD");
                        },
                        editComponent: props => (
                          <TextField
                            id="created_at_time"
                            label="Dia"
                            type="datetime-local"
                            defaultValue={ ( props.value ) ? moment(props.value).format("YYYY-MM-DDTHH:mm") : ''}
                            onChange={e => props.onChange(moment(e.target.value).format("YYYY-MM-DD HH:mm"))}
                            className={classes.textField}
                            InputLabelProps={{
                              shrink: true,
                            }}
                          />
                        ),
                      },
                      { title: 'Entrada',
                        field: 'checkin',
                        editComponent: props => (
                          <TextField
                            id="checkin_time"
                            label="Entrada"
                            type="datetime-local"
                            defaultValue={ ( props.value ) ? moment(props.value).format("YYYY-MM-DDTHH:mm") : ''}
                            className={classes.textField}
                            onChange={e => props.onChange(moment(e.target.value).format("YYYY-MM-DD HH:mm"))}
                            InputLabelProps={{
                              shrink: true,
                            }}
                          />
                        ),
                        render: rowData => {
                          return moment(rowData.checkin).format("HH:mm:ss");
                        }
                      },
                      { title: 'Salida',
                        field: 'checkout',
                        type: 'numeric',
                        editComponent: props => (
                          <TextField
                            id="checkout_time"
                            label="Salida"
                            type="datetime-local"
                            defaultValue={ ( props.value ) ? moment(props.value).format("YYYY-MM-DDTHH:mm") : ''}
                            onChange={e => props.onChange(moment(e.target.value).format("YYYY-MM-DD HH:mm"))}
                            className={classes.textField}
                            InputLabelProps={{
                              shrink: true,
                            }}
                          />
                        ),
                        render: rowData => {
                          return moment(rowData.checkout).format("HH:mm:ss");
                        }
                      },
                    ]}
                    options={{
                      search: false,
                      exportMenu: [
                        {
                          label: 'Excel',
                          exportFunc: () => {setOpenExport(true)}
                        }
                      ],
                      actionsColumnIndex: -1,

                    }}
                    tableRef={tableRef}
                    localization={materialTableLocaleES}
                    data={query => getAllAssistences(dataAssociate.id, query)}
                    title="Asistencias"
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
                            </div>
                        ),
                    }}
                    editable={{
                      onRowAdd: newData =>
                        new Promise((resolve, reject) => {
                          setTimeout(() => {
                            setAction('new');
                            setOpenPassword(true);
                            setDataRegister(newData);
                            resolve();
                          }, 1000)
                        }),
                      onRowUpdate: (newData, oldData) =>
                        new Promise((resolve, reject) => {
                          setTimeout(() => {
                            setAction('update');
                            setOpenPassword(true);
                            setDataRegister(newData);
                          }, 1000)
                        }),
                    }}
                  />
                </Grid>
              </Grid>
            </DialogContent>
            <DialogActions>
              <Button onClick={handleClose} color="primary">
                  Cerrar
              </Button>
            </DialogActions>
          </Dialog>
        <Dialog
          open={openPassword}
          onClose={handleClosePassword}
          aria-labelledby="responsive-dialog-title"
        >
          <DialogTitle id="responsive-dialog-title">{"Para cambiar el registro es necesaria su contraseña de acceso"}</DialogTitle>
          <DialogContent>
            <TextField
              id="filled-multiline-static"
              autoFocus
              fullWidth
              required
              label="Comentarios"
              multiline
              rows={4}
              value={comments}
              onChange={e => setComments(e.target.value)}
              variant="filled"
              style={{marginBottom:'10px '}}
            />
            <Typography className={classes.messagePwd} style={{color: 'red'}} variant="caption" display="block" gutterBottom>
              { validateComments }
            </Typography>
            <TextField
              label="Ingresa tu contraseña"
              type="password"
              fullWidth
              value={password}
              onChange={e => setPassword(e.target.value)}
              InputLabelProps={{
                shrink: true,
              }}
              onKeyPress = { (event) => {
                if(event.key === 'Enter'){
                  setPassword(event.target.value);
                  validatePassword();
                }
              }}
            />
            <Typography className={classes.messagePwd} style={{color: 'red'}} variant="caption" display="block" gutterBottom>
              { messagePassword }
            </Typography>
          </DialogContent>
          <DialogActions>
            <Button autoFocus onClick={handleClosePassword} color="primary">
              Cancelar
            </Button>
            <Button onClick={validatePassword} color="primary" autoFocus>
              Validar
            </Button>
          </DialogActions>
        </Dialog>
        <Dialog
          open={openExport}
          onClose={handleCloseExport}
          aria-labelledby="responsive-dialog-title"
        >
          <DialogTitle id="responsive-dialog-title">{"Seleccione rango de fechas para el reporte"}</DialogTitle>
          <DialogContent>
            <Grid container spacing={3}>
              <Grid item xs={6} md={6}>
                <TextField
                  fullWidth
                  id="date-Init"
                  label="Fecha Inicio"
                  type="date"
                  onChange={e => setSelectedDateInitExcel(e.target.value)}
                  InputProps={{
                    inputComponent: InputComponentInitExcel,
                  }}
                  defaultValue={selectedDateInitExcel}
                  InputLabelProps={{
                    shrink: true,
                  }}
                />
              </Grid>
              <Grid item xs={6} md={6}>
                <TextField
                  fullWidth
                  id="date-Init"
                  label="Fecha Fin"
                  type="date"
                  onChange={e => setSelectedDateEndExcel(e.target.value)}
                  InputProps={{
                    inputComponent: InputComponentEndExcel,
                  }}
                  defaultValue={selectedDateEndExcel}
                  InputLabelProps={{
                    shrink: true,
                  }}
                />
              </Grid>
            </Grid>
            <Typography className={classes.messagePwd} style={{color: 'red'}} variant="caption" display="block" gutterBottom>
              { messagePassword }
            </Typography>
          </DialogContent>
          <DialogActions>
            <Button autoFocus onClick={handleCloseExport} color="primary">
              Cancelar
            </Button>
            <Button onClick={getReport} color="primary" autoFocus>
              Enviar
            </Button>
          </DialogActions>
        </Dialog>
        <Dialog
            open={openExportGeneral}
            onClose={handleCloseExportGeneral}
            aria-labelledby="responsive-dialog-title"
        >
            <DialogTitle id="responsive-dialog-title">{"Seleccione rango de fechas para el reporte"}</DialogTitle>
            <DialogContent>
                <Grid container spacing={3}>
                    <Grid item xs={6} md={6}>
                        <TextField
                            fullWidth
                            id="date-Init"
                            label="Fecha Inicio"
                            type="date"
                            onChange={e => setSelectedDateInitExcelGeneral(e.target.value)}
                            InputProps={{
                                inputComponent: InputComponentInitExcelGeneral,
                            }}
                            defaultValue={selectedDateInitExcelGeneral}
                            InputLabelProps={{
                                shrink: true,
                            }}
                        />
                    </Grid>
                    <Grid item xs={6} md={6}>
                        <TextField
                            fullWidth
                            id="date-Init"
                            label="Fecha Fin"
                            type="date"
                            onChange={e => setSelectedDateEndExcelGeneral(e.target.value)}
                            InputProps={{
                                inputComponent: InputComponentEndExcelGeneral,
                            }}
                            defaultValue={selectedDateEndExcelGeneral}
                            InputLabelProps={{
                                shrink: true,
                            }}
                        />
                    </Grid>
                </Grid>
                <Typography className={classes.messagePwd} style={{color: 'red'}} variant="caption" display="block" gutterBottom>
                    { messagePassword }
                </Typography>
            </DialogContent>
            <DialogActions>
                <Button autoFocus onClick={handleCloseExportGeneral} color="primary">
                    Cancelar
                </Button>
                <Button onClick={getReportGeneral} color="primary" autoFocus>
                    Enviar
                </Button>
            </DialogActions>
        </Dialog>
      </div>
  );
};

Index.layout = page => <Layout title="Reportes" children={page} />;

export default Index;
