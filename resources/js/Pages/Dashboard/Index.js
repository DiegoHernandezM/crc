import React, {useEffect, PureComponent }  from 'react';
import Layout from '@/Shared/Layout';
import clsx from "clsx";
import {Typography, Grid, TextField, FormControl} from '@material-ui/core';
import Card from "../../Shared/DashboardComponents/Card/Card.js";
import CardHeader from "../../Shared/DashboardComponents/Card/CardHeader.js";
import CardBody from "../../Shared/DashboardComponents/Card/CardBody.js";
import CardIcon from "../../Shared/DashboardComponents/Card/CardIcon.js";
import materialTableLocaleES from "../../Shared/MaterialTableLocateES";
import {MTableToolbar} from "@material-table/core";
import MaterialTable from "@material-table/core";


import {
    getBestAssociatesWeek, getBestAbsencesWeek,
    getBestAbsencesByDay, getSorterProdWeek,
    getPickingProdWeek,
} from "../../Api/CheckinService/CheckinApi";
import Paper from "@material-ui/core/Paper";
import { makeStyles } from "@material-ui/core/styles";

import {
    BarChart,
    Bar,
    LineChart,
    Line,
    XAxis,
    YAxis,
    Label,
    Tooltip,
    ResponsiveContainer
} from "recharts";
import { useTheme } from "@material-ui/core/styles";
import {usePage} from "@inertiajs/inertia-react";


const useStyles = makeStyles(theme => ({
    paper: {
        padding: theme.spacing(2),
        display: "flex",
        overflow: "auto",
        flexDirection: "column",
    },
    fixedHeight: {
        height: 400
    }
}));

const Dashboard = () => {
  const { auth } = usePage().props;
  const [bestHours, setBestHours] = React.useState([]);
  const [absences, setAbsences] = React.useState([]);
  const theme = useTheme();
  const classes = useStyles();
  const fixedHeightPaper = clsx(classes.paper, classes.fixedHeight);
  const [dataBest, setDataBest] = React.useState([]);
  const [dataAbsences, setDataAbsences] = React.useState([]);
  const [dataProdSorter, setDataProdSorter] = React.useState([]);
  const [dataProdPicking, setDataProdPicking] = React.useState([]);

  useEffect(() => {
    getBestAssociatesWeek()
      .then(response => {
        setBestHours(response.data);
      })
      .catch(error => {
        console.log(error)
      });
      getBestAbsencesByDay()
        .then(response => {
          if (response.data.length > 0 ) {
              setDataAbsences(response.data);
          }
      })
      .catch(error => {
          console.log(error)
      });
      getSorterProdWeek()
        .then(response => {
          if (response.data.length > 0 ) {
            setDataProdSorter(response.data);
          }
        })
        .catch(error => {
          console.log(error)
        });
      getPickingProdWeek()
          .then(response => {
              if (response.data.length > 0 ) {
                  setDataProdPicking(response.data);
              }
          })
          .catch(error => {
              console.log(error)
          });
  }, []);

  return (
    <div>
      <h1 className="mb-8 text-3xl font-bold">PANEL DE CONTROL</h1>
      <Grid container spacing={1} direction="row" justifyContent="center" alignItems="baseline">
        <Grid item md={6} xs={12}>
          <Card style={{marginTop:"0px"}}>
            <CardHeader color="success" stats icon>
              <CardIcon color="success">
                <h1>HORAS EXTRA ESTA SEMANA</h1>
              </CardIcon>
              <Typography variant="h6" color="primary">
              </Typography>
            </CardHeader>
              <Paper  className={fixedHeightPaper}>
                  <MaterialTable
                      columns={[
                          { title: 'No. Empleado', field: 'employee_number'},
                          { title: 'Nombre', field: 'name'},
                          { title: 'Area', field: 'subarea'},
                          { title: 'Horas Extra', field: 'total'},
                      ]}
                      options={{
                          search: false,
                          actionsColumnIndex: -1,
                          showTitle: false,
                          toolbar: false,
                          padding: 'dense',
                          pageSize: 10,
                      }}
                      localization={materialTableLocaleES}
                      data={bestHours}
                      title="Horas extra"
                  />
              </Paper>
          </Card>
        </Grid>
        <Grid item md={6} xs={12}>
          <Card style={{marginTop:"0px"}}>
            <CardHeader color="success" stats icon>
              <CardIcon color="success">
                <h1>FALTAS ESTA SEMANA</h1>
              </CardIcon>
              <Typography variant="h6" color="primary">
              </Typography>
            </CardHeader>
            <Paper  className={fixedHeightPaper} elevation={2}>
              <ResponsiveContainer>
                <BarChart data={dataAbsences}>
                  <XAxis dataKey="day"  />
                  <YAxis />
                  <Tooltip  />
                  <Bar dataKey="absences" fill="rgba(106, 110, 229)" />
                </BarChart>
              </ResponsiveContainer>
            </Paper>
          </Card>
        </Grid>
          {(auth.user.area === 2 || auth.user.area === null)  &&
            <Grid item md={(auth.user.area === 2 || auth.user.area === 1)? 12 : 6} xs={12}>
              <Card style={{marginTop:"0px"}}>
                  <CardHeader color="success" stats icon>
                      <CardIcon color="success">
                          <h1>PRODUCTIVIDAD SORTER</h1>
                      </CardIcon>
                      <Typography variant="h6" color="primary">
                      </Typography>
                  </CardHeader>
                  <Paper  className={fixedHeightPaper} elevation={2}>
                      <ResponsiveContainer>
                          <LineChart
                              data={dataProdSorter}
                              margin={{
                                  top: 16,
                                  right: 16,
                                  bottom: 0,
                                  left: 24
                              }}
                          >
                              <XAxis dataKey="day" stroke={theme.palette.text.secondary}/>
                              <YAxis stroke={theme.palette.text.secondary}>
                                  <Label
                                      angle={270}
                                      position="left"
                                      style={{ textAnchor: "middle", fill: theme.palette.text.primary }}
                                  >
                                      PPK
                                  </Label>
                              </YAxis>
                              <Tooltip/>
                              <Line
                                  type="monotone"
                                  dataKey="ppk"
                                  stroke={theme.palette.success.main}
                                  dot={false}
                              />
                          </LineChart>
                      </ResponsiveContainer>
                  </Paper>
              </Card>
            </Grid>
          }
          {(auth.user.area === 1 || auth.user.area === null) &&
            <Grid item md={(auth.user.area === 2 || auth.user.area === 1)? 12 : 6} xs={12}>
              <Card style={{marginTop:"0px"}}>
                  <CardHeader color="success" stats icon>
                      <CardIcon color="success">
                          <h1>PRODUCTIVIDAD PICKING</h1>
                      </CardIcon>
                      <Typography variant="h6" color="primary">
                      </Typography>
                  </CardHeader>
                  <Paper  className={fixedHeightPaper} elevation={2}>
                      <ResponsiveContainer>
                          <LineChart
                              data={dataProdPicking}
                              margin={{
                                  top: 16,
                                  right: 16,
                                  bottom: 0,
                                  left: 24
                              }}
                          >
                              <XAxis dataKey="day" stroke={theme.palette.text.secondary} />
                              <YAxis stroke={theme.palette.text.secondary}>
                                  <Label
                                      angle={270}
                                      position="left"
                                      style={{ textAnchor: "middle", fill: theme.palette.text.primary }}
                                  >
                                      CAJAS
                                  </Label>
                              </YAxis>
                              <Tooltip/>
                              <Line
                                  type="monotone"
                                  dataKey="boxes"
                                  stroke={theme.palette.success.main}
                                  dot={false}
                              />
                          </LineChart>
                      </ResponsiveContainer>
                  </Paper>
              </Card>
            </Grid>
          }
      </Grid>
    </div>
  );
};
Dashboard.layout = page => <Layout title="Dashboard" children={page} />;

export default Dashboard;
