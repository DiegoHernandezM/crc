import React, { useEffect }  from 'react';
import Layout from '@/Shared/Layout';
import { makeStyles } from '@material-ui/core/styles';
import {
  Grid,
  Card,
  CardActionArea,
  CardContent,
  CardMedia,
  TextField,
  Typography,
  Paper,
} from '@material-ui/core';
import MaterialTable from '@material-table/core';
import {usePage} from "@inertiajs/inertia-react";
import materialTableLocaleES from "../../Shared/MaterialTableLocateES";
import Alert from '@material-ui/lab/Alert';
import Collapse from '@material-ui/core/Collapse';
import CloseIcon from '@material-ui/icons/Close';
import IconButton from '@material-ui/core/IconButton';

import axios from 'axios';
import moment from "moment";

const useStyles = makeStyles(theme => ({
  root: {
    flexGrow: 1,
  },
  media: {
    height: 200,
  },
  clock: {
    align: 'center',
    color: 'white',
    fontSize: '3rem',
    height: '200px',
    width: '200px',
    backgroundColor: '#337f59',
    display: 'flex',
    justifyContent: 'center',
    alignItems: 'center',
    borderRadius: '50%',
  },
  formControlTableToolBar: {
    backgroundColor: 'white',
    minWidth: '80%',
  },
  paper: {
    padding: theme.spacing(3),
  },
}));

const Index = () => {

  const classes = useStyles();
  const tableRef = React.createRef();
  const [open, setOpen] = React.useState(false);
  const [message, setMessage] = React.useState('');
  const { check } = usePage().props;
  const [ data, setData ] = React.useState(check);
  const [ associate, setAssociate ] = React.useState([]);
  const [ userImage, setUserImage] = React.useState('/person.png');
  const [ clock, setClock ] = React.useState(new Date().toLocaleTimeString());
  const [ numEmpleado, setNumEmpleado ] = React.useState('');

  useEffect(() => {
    const intervalClock = setInterval(() => {
      setClock(new Date().toLocaleTimeString());
    }, 1000);
    return () => clearInterval(intervalClock);
  }, []);
  const checkAssociate = e => {
    setNumEmpleado(e.target.value);
  };
  const changeInput = e => {
    if(e.key === 'Enter') {
      axios.get(route('checkin.check', {'employee_number': numEmpleado})).then((res) => {
        if (res.data.message) {
          setMessage(res.data.message);
          setOpen(true);
        } else {
          setData(res.data.associates);
          setAssociate(res.data.associate);
          setMessage(res.data.message);
          setOpen(false);
          if (res.data.associate.picture != null) {
            setUserImage(res.data.associate.picture);
          }
        }
      });
      setNumEmpleado('');
    }
  };

  return (
    <div className={classes.root} >
      <Grid
        container
        spacing={2}
        direction="column"
        alignItems="center"
        justifyContent="center"
        style={{ marginTop:"-55px" }}
      >
        <Grid item xs={12} md={6}>
          <Card className={classes.root}>
            <CardActionArea>
              <CardContent>
                <h1 className={classes.clock}> { clock } </h1>
              </CardContent>
            </CardActionArea>
          </Card>
        </Grid>
      </Grid>
      <Grid container spacing={3} >
        <Grid item xs={12} md={3}>
          <Paper className={classes.paper} >
            <TextField
              autoFocus
              fullWidth
              style={{ marginBottom: '5px' }}
              error={false}
              id="outlined-error-helper-text"
              label="No. de empleado"
              variant="outlined"
              value={numEmpleado}
              onChange={checkAssociate}
              onKeyPress={changeInput}
            />
            <Collapse in={open}>
              <Alert severity="error"
               action={
                 <IconButton
                   aria-label="close"
                   color="inherit"
                   size="small"
                   onClick={() => {
                       setOpen(false);
                   }}
                 >
                   <CloseIcon fontSize="inherit" />
                 </IconButton>
               }
              >
                { message }
              </Alert>
            </Collapse>
            <Card className={classes.root}>
              <CardActionArea>
                <CardMedia
                  className={classes.media}
                  image={userImage}
                  title="CCP"
                  style={{ height:'300px',margin:'0 auto' }}
                />
                <CardContent>
                  <Typography gutterBottom variant="h5" component="h2">
                    {associate.name}
                  </Typography>
                </CardContent>
              </CardActionArea>
            </Card>
          </Paper>
          </Grid>
        <Grid item xs={12} md={9}>
          <MaterialTable
            tableRef={tableRef}
            columns={[
              { title: 'No. empleado', field: 'employee_number' },
              { title: 'Nombre', field: 'associate' },
              { title: 'Area', field: 'area' },
              { title: 'Horario', field: 'shift' },
              { title: 'Entrada',
                field: 'checkin',
                render: rowData => {
                  return moment(rowData.checkin).format("HH:mm:ss");
                }
              },
              { title: 'Salida',
                  field: 'checkout',
                  render: rowData => {
                      if (rowData.checkout) {
                          return moment(rowData.checkout).format("HH:mm:ss");
                      } else {
                        return '';
                      }
                  }
              },
            ]}
            options={{
              search: false,
              showTitle: false,
              toolbar: false,
              padding: "dense",
            }}
            data={ data }
            title="Demo Title"
            localization={materialTableLocaleES}
          />
        </Grid>
      </Grid>

    </div>
  );
};

Index.layout = page => <Layout title="Checkin" children={page} />;

export default Index;
