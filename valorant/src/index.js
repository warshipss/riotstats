const util = require('util')
const http = require('http')
const path = require('path')
const Axios = require('axios')
const dotenv = require('dotenv')
const express = require('express')
const find = require('find-process')
const condense = require('selective-whitespace')
const exec = util.promisify(require('child_process').exec)

dotenv.config({
  path: path.resolve(__dirname, '../.env')
})

let riotService = null

const axios = Axios.create({
  headers: {
    'content-type': 'application/json',
    'authorization': 'Basic ' + process.env.VALORANT_API_KEY,
    'x-riot-clientversion': 'release-0.47-shipping-36-420086',
    'user-agent': 'ShooterGame/36 Windows/10.0.18362.1.256.64bit',
  }
})

const getPortUsage = async () => {
  let { stdout } = await exec('netstat.exe -a -n -o')

  return condense(stdout, { keep: '\n' }).split('\n').slice(4).map(i => i.trim().split(' '))
}

const response = (res, data = {}) => {
  res.statusCode = 200
  res.setHeader('Content-Type', 'application/json')
  res.send(JSON.stringify(data, null, 2))
}

const findService = async () => {
  if (riotService) {
    return riotService
  }

  const usage = await getPortUsage()
  const process = await find('name', 'RiotClientServices')

  if (! process.length) {
    return null
  }

  const ports = usage.filter(u => parseInt(u[4]) === parseInt(process[0].pid))

  for (const port of ports)
  {
    if (port[0] === 'TCP' && port[1].indexOf('127.0.0.1') === 0 && ! riotService)
    {
      const data = await axios.get(`https://${port[1]}/process-control/v1/process`, {
        timeout: 100,
      }).catch(e => {})

      if (data) {
        riotService = 'https://' + port[1]
      }
    }
  }

  return riotService
}

const app = express();

app.get('/port', async (req, res) => {
  if (! riotService) {
    return response(res, { error: 'riotService is null' })
  }

  return response(res, { riotService })
})

app.get('/token', async (req, res) => {
  const { data } = await axios.get(riotService + '/rso-auth/v1/authorization/access-token')

  return response(res, data)
})

app.get('/uid', async (req, res) => {
  const { data } = await axios.post(riotService + '/chat/v4/friendrequests', {
    game_tag: req.query.tag,
    game_name: req.query.name
  }, {
    headers: {
      'rchat-blocking': 'true',
    }
  }).catch(e => {
    response(res, { error: 'Unknown error' })
  })

  if (! data || ! data.hasOwnProperty('requests') || ! data.requests.length)
  {
    if (! res.headersSent) {
      return response(res, { error: 'User not found' })
    }

    return false
  }

  const requests = data.requests

  if (! requests.length) {
    return response(res, { error: 'User not found' })
  }

  const uid = requests[0].pid.split('@')[0]

  return response(res, { uid })
})

findService().then(() => {
  app.listen(3000, function () {
    console.log(`Service discovered at ${riotService}, listening 3000`);
  });
})
