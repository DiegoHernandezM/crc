import React from 'react';
import { makeStyles } from '@material-ui/core/styles';

// Components
import MainMenuItem from '@/Shared/MainMenuItem';
import List from '@material-ui/core/List';
import ListItem from '@material-ui/core/ListItem';
import Collapse from '@material-ui/core/Collapse';
import ExpandLess from '@material-ui/icons/ExpandLess';
import ExpandMore from '@material-ui/icons/ExpandMore';
import { usePage } from '@inertiajs/inertia-react'

const useStyles = makeStyles((theme) => ({
    collapseStyle : {
      marginTop: "-20px",
      marginLeft:"-20px"
    },
    root: {
        backgroundColor: theme.palette.background.paper,
        color:'white',
    },
    nested: {
        marginTop:"-30px",
        paddingLeft: theme.spacing(4),
    },
    expandStyle: {
        marginTop: "-15px",
        marginLeft: "15px",
        color: "#DDD5D3 "
    }

}));

export default ({ className }) => {

  const classes = useStyles();
  const { auth } = usePage().props;
  const [open, setOpen] = React.useState(false);
  const [openReport, setOpenReport] = React.useState(false);
  const [openProductivity, setOpenProductivity] = React.useState(false);
  const [denseReport, setDenseReport] = React.useState(false);
  const [dense, setDense] = React.useState(false);
  const handleClick = () => {
    setOpen(!open);
  };
  const handleClickReport = () => {
    setOpenReport(!openReport);
  };
  const handleClickProductivity = () => {
    setOpenProductivity(!openProductivity);
  };

  return (
    <div className={className}>
    { auth.user.can.Dashboard ?
      <MainMenuItem text="Dashboard" link="dashboard" icon="dashboard" />
      : null
    }
    {
      Object.keys(auth.user.can).filter(p => p.startsWith('Asociados')).length > 0 &&
      <MainMenuItem text="Asociados" link="associates" icon="office" />
    }  
    {
      auth.user.can.Checador ? 
      <MainMenuItem text="Checador" link="checkin" icon="users" />
      : null
    } 
    {
      Object.keys(auth.user.can).filter(p => p.startsWith('Productividad')).length > 0 ?
      <List dense={dense} className={classes.collapseStyle}>
        <ListItem onClick={handleClickProductivity}>
            <MainMenuItem text="Productividad" icon="productivity" />
                {openProductivity ? <ExpandLess className={classes.expandStyle} /> : <ExpandMore className={classes.expandStyle} />}
        </ListItem>
        <Collapse in={openProductivity} timeout="auto" unmountOnExit>
          <List component="div">
          {
            auth.user.can['Productividad.Tablero'] &&
            <ListItem className={classes.nested}>
              <MainMenuItem className={classes.nested} text="Tablero" link="board" icon="cash" />
            </ListItem>

          }
          {
            auth.user.can['Productividad.Horas Extra'] && 
            <ListItem className={classes.nested}>
              <MainMenuItem className={classes.nested} text="Horas Extra" link="reports.extrahours" icon="extra-hour" />
            </ListItem>
          }
          {
            auth.user.can['Productividad.Picking'] && 
            <ListItem className={classes.nested}>
              <MainMenuItem className={classes.nested} text="Picking" link="reports.picking" icon="picking" />              
            </ListItem>
          }
          {
            auth.user.can['Productividad.Plantilla'] && 
            <ListItem className={classes.nested}>              
              <MainMenuItem className={classes.nested} text="Plantilla" link="teams" icon="teams" />
            </ListItem>
          }  
          {
            auth.user.can['Productividad.Sorter'] &&
            <ListItem className={classes.nested}>
              <MainMenuItem className={classes.nested} text="Sorter" link="reports.sorter" icon="sorter" />
            </ListItem>
          }
          </List>
        </Collapse>
      </List> 
      : null
    }  
    {
      Object.keys(auth.user.can).filter(p => p.startsWith('Reportes')).length > 0 ? 
      <List dense={denseReport}  className={classes.collapseStyle}>
        <ListItem onClick={handleClickReport}>
          <MainMenuItem text="Reportes" icon="printer" />
            {openReport ? <ExpandLess className={classes.expandStyle} /> : <ExpandMore className={classes.expandStyle} />}
        </ListItem>
        <Collapse in={openReport} timeout="auto" unmountOnExit>
          {
            auth.user.can['Reportes.Asistencias'] && 
            <List component="div">
              <ListItem className={classes.nested}>
                <MainMenuItem className={classes.nested} text="Asistencias" link="reports.shift" icon="asistence" />
              </ListItem>
            </List>
          }
        </Collapse>
      </List>
      : null
    }
    {
      Object.keys(auth.user.can).filter(p => p.startsWith('Catalogos')).length > 0 ? 
      <List dense={dense} className={classes.collapseStyle}>
        <ListItem onClick={handleClick}>
            <MainMenuItem text="Catálogos" icon="book" />
            {open ? <ExpandLess className={classes.expandStyle} /> : <ExpandMore className={classes.expandStyle} />}
        </ListItem>
        <Collapse in={open} timeout="auto" unmountOnExit>
            <List component="div">
                <ListItem className={classes.nested}>
                    <MainMenuItem className={classes.nested} text="Horarios" link="shifts" icon="clock" />
                </ListItem>
                <ListItem className={classes.nested}>
                    <MainMenuItem className={classes.nested} text="Tipo asociado" link="typeassociate" icon="associate" />
                </ListItem>
                <ListItem className={classes.nested}>
                    <MainMenuItem className={classes.nested} text="Area" link="area" icon="area" />
                </ListItem>
                <ListItem className={classes.nested}>
                    <MainMenuItem className={classes.nested} text="Subarea" link="subarea" icon="subarea" />
                </ListItem>
            </List>
        </Collapse>
      </List>
      : null
    }  
      

    </div>
  );
};
