import React from 'react';
import Button from '@material-ui/core/Button';
import Dialog from '@material-ui/core/Dialog';
import DialogActions from '@material-ui/core/DialogActions';
import DialogContent from '@material-ui/core/DialogContent';
import DialogContentText from '@material-ui/core/DialogContentText';
import DialogTitle from '@material-ui/core/DialogTitle';
import TextInput from "@/Shared/TextInput";
import {useForm} from "@inertiajs/inertia-react";

export default function ChangeShift({open, close, range}) {
  function handleSubmit(e) {
    e.preventDefault();
    post(route('range'));
  }
  const { data, setData, errors, post, processing } = useForm({
    day: range ? range.day : ''
  });

  return (
    <div>
      <Dialog
        open={open}
        onClose={close}
        aria-labelledby="form-dialog-title"
      >
        <form name="createForm" onSubmit={handleSubmit}>
          <DialogTitle id="alert-dialog-title">Ingresa el rango de fechas</DialogTitle>
          <DialogContent>
            <DialogContentText id="alert-dialog-description">
              Ingresa o reasigna la fecha del proximo cambio de horario automatico
            </DialogContentText>
            <TextInput
              fullWidth
              required
              id="date-Init"
              label="Dia"
              type="date"
              variant="outlined"
              onChange={e => setData('day', e.target.value)}
              defaultValue={data.day}
            />
          </DialogContent>
          <DialogActions>
            <Button onClick={close} color="primary">
              Cancelar
            </Button>
            <Button type="submit" onClick={close} color="primary" autoFocus>
              Aceptar
            </Button>
          </DialogActions>
        </form>
      </Dialog>
    </div>
  );
}
