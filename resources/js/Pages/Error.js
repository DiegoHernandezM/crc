import React from 'react';
import Helmet from 'react-helmet';
// import { usePage } from '@inertiajs/inertia-react';

export default ({ status }) => {
  // const { status } = usePage().props;

  const title = {
    503: '503: Servicio no disponible',
    500: '500: Error de servicio',
    404: '404: Página no encontrada',
    403: '403: No permitido'
  }[status];

  const description = {
    503: 'Lo siento, estamos realizando mantenimiento. Por favor vuelva más tarde.',
    500: 'Ups, algo salió mal en nuestro servicio.',
    404: 'Lo siento, la página que estás buscando no pudo ser encontrada.',
    403: 'No tienes permiso para ver ésto :('
  }[status];

  return (
    <div className="flex items-center justify-center min-h-screen p-5 text-indigo-100 bg-indigo-800">
      <Helmet title={title} />
      <div className="w-full max-w-md">
        <h1 className="text-3xl">{title}</h1>
        <p className="mt-3 text-lg leading-tight">{description}</p>
      </div>
    </div>
  );
};
