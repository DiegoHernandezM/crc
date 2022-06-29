import React, { useState, useRef, useEffect } from 'react';
import materialTableLocaleES from "../../Shared/MaterialTableLocateES";
import { usePage } from '@inertiajs/inertia-react';
import { makeStyles } from '@material-ui/core/styles';
import moment from "moment";
// api
import { getSubareas, getAssociates, moveTeam } from "../../Api/CheckinService/CheckinApi";
// components
import Layout from '@/Shared/Layout';
import MuiAlert from '@material-ui/lab/Alert';
import { Close as CloseIcon } from "@material-ui/icons";
import MaterialTable, { MTableToolbar } from "@material-table/core";
import {
    AppBar,
    FormControl,
    InputLabel,
    Grid,
    IconButton,
    Button,
    MenuItem,
    Toolbar,
    Typography,
    Select,
    Snackbar,
    Slide,
    Dialog,
} from "@material-ui/core";
import ChangeShift from "./components/ChangeShift";

const useStyles = makeStyles(theme => ({
  actionDescriptionEdit: {
    '& .MuiInput-root': {
      fontSize: 'small',
    },
  },
  root: {
    marginTop: '15px',
    marginLeft: '19px',
    marginRight: '19px',
    flexGrow: 1,
  },
  appBar: {
    position: 'relative',
  },
  paper: {
    padding: theme.spacing(3),
    textAlign: 'center',
    color: theme.palette.text.secondary,
  },
  typography: {
    textAlign: 'center',
  },
  formControlTableToolBar: {
    margin: theme.spacing(1),
    marginTop: -theme.spacing(7),
    marginLeft: theme.spacing(35),
    minWidth: 160,
    maxWidth: 10,
  },

  formControl0TableToolBar: {
    margin: theme.spacing(1),
    marginTop: -theme.spacing(7),
    marginLeft: theme.spacing(2),
    minWidth: 160,
    maxWidth: 10,
  },
  formControl2TableToolBar: {
    margin: theme.spacing(1),
    marginTop: theme.spacing(-6),
    marginLeft: theme.spacing(2),
    minWidth: 150,
    maxWidth: 10,
  },
  formControl3TableToolBar: {
    margin: theme.spacing(1),
    marginTop: theme.spacing(-7),
    marginLeft: theme.spacing(8),
    minWidth: 180,
    maxWidth: 10,
  },
  formControl4TableToolBar: {
    margin: theme.spacing(1),
    marginTop: theme.spacing(-6),
    marginLeft: theme.spacing(8),
    minWidth: 150,
    maxWidth: 10,
  },
  formControl5TableToolBar: {
    margin: theme.spacing(1),
    marginTop: theme.spacing(-7),
    marginLeft: theme.spacing(2),
    minWidth: 180,
    maxWidth: 10,
  },
}));

function Alert(props) {
  return <MuiAlert elevation={6} variant="filled" {...props} />;
}

const Transition = React.forwardRef(function Transition(props, ref) {
  return <Slide direction="up" ref={ref} {...props} />;
});

const Index = () => {
  const classes = useStyles();
  const { shifts, range, auth } = usePage().props;
  const [subareas, setSubareas] = useState([]);
  const [snackOpen, setSnackOpen] = useState(false);
  const [selectedAssociates, setSelectedAssociates] = useState([]);
  const [openModal, setOpenModal] = useState(false);
  const [snackMessage, setSnackMessage] = useState("");
  const [alertType, setAlertType] = useState("success");
  const tableRef = useRef();
  const tableRef2 = useRef();
  const [selectedSubarea, setSelectedSubarea] = useState(-1);
  const [selectedShift, setSelectedShift] = useState(-1);
  const [selectedSubareaDest, setSelectedSubareaDest] = useState(1);
  const [selectedShiftDest, setSelectedShiftDest] = useState(1);
  const [openShift, setOpenShift] = useState(false);

  const handleChangeSubarea = event => {
    if (tableRef.current) {
      tableRef.current.state.query = {
        ...tableRef.current.state.query,
        page: 0,
        status: event.target.value
      };
      setSelectedSubarea(event.target.value);
      tableRef.current.onQueryChange();
    }
  };

  const handleChangeShift = event => {
    if (tableRef.current) {
      tableRef.current.state.query = {
        ...tableRef.current.state.query,
        page: 0,
        shift: event.target.value
      };
      setSelectedShift(event.target.value);
      tableRef.current.onQueryChange();
    }
  };

  const handleChangeSubareaDest = event => {
    setSelectedSubareaDest(event.target.value);
  };

  const handleChangeShiftDest = event => {
    setSelectedShiftDest(event.target.value);
  };

  const handleChangeTeam = (data) => {
    setSelectedAssociates(data);
    toggleModal();
  };

  const handleCloseSnack = (event, reason) => {
    if (reason === 'clickaway') {
      return;
    }
    setSnackOpen(false);
  };

  const handleChangeTeamSubmit = () => {
    const associateIds = selectedAssociates.map(function(v){
      return v.id;
    });
    moveTeam(associateIds, selectedSubareaDest, selectedShiftDest)
      .then(response => {
        setAlertType(response.success == true ? 'success' : 'error');
        setSnackMessage(response.message);
        setSnackOpen(true);
        tableRef.current.onQueryChange();
        toggleModal();
      })
  };

  const toggleModal = () => {
      setOpenModal(!openModal);
  };

  const getAssociatesData = (query) => {
    return getAssociates(query.status, query.shift, query.search)
      .then(response => {
        return response;
      })
      .then(response => ({
        ...query,
        data: response,
      }))
      .catch(() => ({
        ...query,
        page: 0,
        pageSize: 15,
        totalCount: 0,
        data: [],
      }));
  };

  const openShiftDialog = () => {
    setOpenShift(true);
  };

  const closeShiftDialog = () => {
    setOpenShift(false);
  };

  useEffect(() => {
    getSubareas().then(response => {
      setSubareas(response);
    }).catch(e => {
      console.log(e);
    })
  }, []);

  return (
    <div>
      <Snackbar open={snackOpen} autoHideDuration={6000} onClose={handleCloseSnack}>
        <Alert onClose={handleCloseSnack} severity={alertType}>
          {snackMessage}
        </Alert>
      </Snackbar>
      <Dialog fullScreen open={openModal} onClose={() => setOpenModal(false)} TransitionComponent={Transition}>
        <AppBar className={classes.appBar}>
          <Toolbar>
            <IconButton edge="start" color="inherit" onClick={() => setOpenModal(false)} aria-label="close">
              <CloseIcon />
            </IconButton>
            <Typography variant="h6" className={classes.title}>
              CAMBIO DE EQUIPOS
            </Typography>
          </Toolbar>
        </AppBar>
        <div className={classes.paper} id="modalTableDiv">
          <MaterialTable
            columns={[
              {
                title: 'No. Empleado',
                field: 'employee_number',
              },
              {
                title: 'Nombre',
                field: 'name',
              },
              {
                title: 'Horario',
                field: 'shift',
              },
              {
                title: 'Equipo',
                field: 'subarea',
              },
              { title: 'Fecha de ingreso', field: 'entry_date',
                render: rowData =>  {
                  return moment(rowData.entry_date).format("YYYY-MM-DD");
                },
              },
            ]}
            title={`Asociados seleccionados`}
            localization={materialTableLocaleES}
            data={selectedAssociates}
            tableRef={tableRef2}
            options={{
              pageSize: 15,
              padding: 'dense',
              pageSizeOptions: [15, 30, 50, 100],
            }}
            components={{
              Toolbar: props => (
                <div>
                  <MTableToolbar {...props} />
                  <FormControl className={classes.formControl2TableToolBar}>
                    <Button variant="contained" color="primary" onClick={handleChangeTeamSubmit}>
                      Aceptar
                    </Button>
                  </FormControl>
                  <FormControl className={classes.formControl3TableToolBar}>
                    <InputLabel id="subLabel">Mover a...</InputLabel>
                    <Select
                      labelId="subLabel"
                      id="demo-simple-select-required"
                      value={selectedSubareaDest}
                      onChange={handleChangeSubareaDest}
                    >
                      {subareas.map(subarea => {
                         return <MenuItem value={subarea.id} key={`subdest${subarea.id}`}>{subarea.name}</MenuItem>
                      })}
                    </Select>
                  </FormControl>
                  <FormControl className={classes.formControl5TableToolBar}>
                    <InputLabel id="subLabel">En turno...</InputLabel>
                    <Select
                      labelId="subLabel"
                      id="demo-simple-select-required"
                      value={selectedShiftDest}
                      onChange={handleChangeShiftDest}
                    >
                      {shifts.map(shift => {
                        return <MenuItem value={shift.id} key={`shiftdest${shift.id}`}>{shift.name}</MenuItem>
                      })}
                    </Select>
                  </FormControl>
                  <FormControl className={classes.formControl4TableToolBar}>
                    <Button variant="contained" color="secondary" onClick={toggleModal}>
                      Cancelar
                    </Button>
                  </FormControl>
                </div>
              ),
            }}
          />
        </div>
      </Dialog>
      <MaterialTable
        columns={[
          {
            title: '',
            field: 'picture',
            render: rowData => {
              let picture = (rowData.picture != null) ? rowData.picture : '/person.png';
              return <img
                style={{ height: 36, borderRadius: '50%' }}
                src={picture}
              />
            }
          },
          { title: 'No. Empleado', field: 'employee_number' },
          { title: 'Nombre', field: 'name' },
          { title: 'Horario', field: 'shift' },
          { title: 'Equipo', field: 'subarea' },
          { title: 'Fecha de ingreso', field: 'entry_date',
            render: rowData =>  {
              return moment(rowData.entry_date).format("YYYY-MM-DD");
            },
          },
        ]}
        tableRef={tableRef}
        data={query => getAssociatesData(query)}
        title="EQUIPOS"
        localization={materialTableLocaleES}
        options={{
          search: true,
          padding: 'dense',
          selection: true,
          selectionProps: rowData => ({
            color: 'primary'
          }),
          showTitle: true,
          actionsColumnIndex: -1,
          paging: false,
          headerStyle: { position: 'sticky', top: 0 },
          maxBodyHeight: window.innerHeight+'px'
        }}
        actions={[
          {
            tooltip: 'Cambio de equipo...',
            icon: 'eject',
            onClick: (evt, data) => {handleChangeTeam(data)}
          }
        ]}
        components={{
          Toolbar: props => (
            <div>
              <MTableToolbar {...props} />
              <FormControl className={classes.formControlTableToolBar}>
                <InputLabel id="statusLabel">Filtrar por equipo</InputLabel>
                <Select
                  labelId="statusLabel"
                  id="demo-simple-select-required"
                  value={selectedSubarea}
                  onChange={handleChangeSubarea}
                >
                  <MenuItem value={-1}>Todos</MenuItem>
                  {subareas.map(subarea => {
                     return <MenuItem value={subarea.id} key={`sub${subarea.id}`}>{subarea.name}</MenuItem>
                  })}
                </Select>
              </FormControl>
              <FormControl className={classes.formControl0TableToolBar}>
                <InputLabel id="shiftLabel">Filtrar por turno</InputLabel>
                <Select
                  labelId="shiftLabel"
                  id="demo-simple-select-required"
                  value={selectedShift}
                  onChange={handleChangeShift}
                >
                  <MenuItem value={-1}>Todos</MenuItem>
                  {shifts.map(shift => {
                     return <MenuItem value={shift.id} key={`sub${shift.id}`}>{shift.name}</MenuItem>
                  })}
                </Select>
              </FormControl>
              <FormControl className={classes.formControl0TableToolBar}>
                <Button
                  variant="contained"
                  size="medium"
                  color="primary"
                  onClick={openShiftDialog}
                  style={{marginTop: '10px', display: auth.user.area !== 1 ? 'block' : 'none'}}
                >
                  Horarios
                </Button>
              </FormControl>
            </div>
          ),
        }}
      />
      <ChangeShift open={openShift} close={closeShiftDialog} range={range}/>
    </div>
  );
};

Index.layout = page => <Layout title="Asociados" children={page} />;

export default Index;
