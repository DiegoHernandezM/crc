import React from 'react';
import { InertiaLink, useForm } from '@inertiajs/inertia-react';
import Layout from '@/Shared/Layout';
import LoadingButton from '@/Shared/LoadingButton';
import TextInput from '@/Shared/TextInput';

const Create = () => {

    const { data, setData, errors, post, processing } = useForm({
        name: '',
    });

    function handleSubmit(e) {
        e.preventDefault();
        post(route('typeassociate.store'));
    }

    return (
        <div>
            <h1 className="mb-8 text-3xl font-bold">
                <InertiaLink
                    href={route('shifts')}
                    className="text-indigo-600 hover:text-indigo-700"
                >
                    Tipo de Asociado
                </InertiaLink>
                <span className="font-medium text-indigo-600"> /</span> Creación
            </h1>
            <div className="max-w-3xl overflow-hidden bg-white rounded shadow">
                <form onSubmit={handleSubmit}>
                    <div className="flex flex-wrap p-8 -mb-8 -mr-6">
                        <TextInput
                            className="w-full pb-8 pr-6 lg:w-1/2"
                            label="Name"
                            name="name"
                            errors={errors.name}
                            value={data.name}
                            onChange={e => setData('name', e.target.value)}
                        />
                    </div>
                    <div className="flex items-center justify-end px-8 py-4 bg-gray-100 border-t border-gray-200">
                        <LoadingButton
                            loading={processing}
                            type="submit"
                            className="btn-indigo"
                        >
                            Guardar
                        </LoadingButton>
                    </div>
                </form>
            </div>
        </div>
    );
};

Create.layout = page => <Layout title="Creacion Tipo de Asociado" children={page} />;

export default Create;
