db = db.getSiblingDB('escuela');

db.estudiantes.insertMany([
  {
    id: 1,
    nombre: 'LOPEZ AGUIRRE SANTIAGO',
    grupo: '102-A',
    f_nac: new Date('2014-12-11')
  },
  {
    id: 2,
    nombre: 'URSUA GUTIERREZ GUILLERMO',
    grupo: '106-A',
    f_nac: new Date('2007-12-21')
  }
]);