const fs = require('fs');

const expData = JSON.parse(fs.readFileSync('scratch_technical/QUIZ_EXPECTED_728.json', 'utf8'));
const cases = expData.cases;
const questionsData = JSON.parse(fs.readFileSync('data/quiz_questions.json', 'utf8')).data;

const byKey = {};
questionsData.forEach(q => {
  byKey[q.key] = {};
  q.options.forEach(opt => {
    byKey[q.key][opt.code] = opt;
  });
});

const optionsById = {};
questionsData.forEach(q => {
  q.options.forEach(opt => {
    optionsById[opt.id] = { ...opt, question_key: q.key, step_number: q.step_number };
  });
});

function simulateResolve(data) {
  let primaryOption = null;
  let skinOption = null;
  let sensOption = null;
  let secondaryOption = null;

  let extractedOptionIds = [];
  const rawOptionList = data.option_ids || data.options || data.selected_options || null;
  if (rawOptionList) {
    const list = Array.isArray(rawOptionList) ? rawOptionList : [rawOptionList];
    list.forEach(item => {
      if (typeof item === 'number' || (typeof item === 'string' && !isNaN(item))) {
        extractedOptionIds.push(parseInt(item));
      } else if (typeof item === 'object' && item !== null) {
        extractedOptionIds.push(item.option_id || item.id || 0);
      }
    });
  }

  extractedOptionIds.forEach(id => {
    const opt = optionsById[id];
    if (opt) {
      if (opt.question_key === 'q1' || opt.step_number === 1) {
        if (!primaryOption) primaryOption = opt;
      } else if (opt.question_key === 'q4' || opt.step_number === 2) {
        if (!secondaryOption) secondaryOption = opt;
      } else if (opt.question_key === 'skin_type' || opt.step_number === 3) {
        if (!skinOption) skinOption = opt;
      } else if (opt.question_key === 'sensitivity' || opt.step_number === 4) {
        if (!sensOption) sensOption = opt;
      }
    }
  });

  // 1. Primary
  let primaryCode = data.primary || data.q1 || null;
  let goalId = data.routine_goal_id || null;
  if (!primaryOption && primaryCode) {
    primaryOption = byKey.q1[primaryCode];
  }
  if (!primaryOption && goalId) {
    primaryOption = Object.values(byKey.q1).find(o => o.mapped_id == goalId);
  }
  if (primaryOption) {
    primaryCode = primaryOption.code;
  }

  // 2. Skin
  let skinCode = data.skin || data.skin_type || null;
  let skinTypeId = data.skin_type_id || null;
  if (!skinOption && skinCode) {
    skinOption = byKey.skin_type[skinCode.toLowerCase()];
  }
  if (!skinOption && skinTypeId) {
    skinOption = Object.values(byKey.skin_type).find(o => o.mapped_id == skinTypeId);
  }
  if (skinOption) {
    skinCode = skinOption.code;
  }

  // 3. Sensitivity
  let isSensitive = false;
  const rawS = data.sensitive !== undefined ? data.sensitive : data.is_sensitive;
  if (rawS !== undefined && rawS !== null) {
    isSensitive = (rawS === true || data.is_sensitive === 'true' || data.is_sensitive === 1 || data.is_sensitive === '1');
  }
  if (sensOption) {
    isSensitive = (sensOption.code === 'yes' || sensOption.mapped_id === 1);
  }

  // 4. Secondary
  let rawSecondary = data.secondary || data.q4 || null;
  let concernIds = data.concern_ids || [];
  if (!Array.isArray(concernIds)) concernIds = [concernIds];
  let secondaryCode = secondaryOption ? secondaryOption.code : null;

  if (rawSecondary && rawSecondary !== 'none' && rawSecondary !== 'null') {
    secondaryCode = rawSecondary.toLowerCase();
  } else if (!secondaryOption && concernIds.length > 0) {
    for (const cid of concernIds) {
      if (!cid || cid === 'none') continue;
      const opt = Object.values(byKey.q4).find(o => o.mapped_id == cid);
      if (opt && opt.code !== 'none' && opt.code !== primaryCode) {
        secondaryOption = opt;
        secondaryCode = opt.code;
        break;
      }
    }
  }

  if (secondaryOption && secondaryOption.code && secondaryOption.code !== 'none') {
    secondaryCode = secondaryOption.code;
  }
  if (secondaryCode === 'none' || secondaryCode === '' || secondaryCode === primaryCode) {
    secondaryCode = 'none';
  }

  return `${primaryCode}|${secondaryCode || 'none'}|${skinCode}|${isSensitive ? 'yes' : 'no'}`;
}

// TEST 1: Full payload (option_ids + IDs)
let p1 = 0;
cases.forEach(c => {
  const [p, s, sk, sn] = c.key.split('|');
  const ops = [byKey.q1[p], byKey.q4[s || 'none'], byKey.skin_type[sk], byKey.sensitivity[sn === 'yes' ? 'yes' : 'no']];
  const payload = {
    skin_type_id: ops[2].mapped_id,
    routine_goal_id: ops[0].mapped_id,
    is_sensitive: ops[3].mapped_id,
    concern_ids: s !== 'none' ? [ops[1].mapped_id] : [],
    option_ids: ops.map(o => o.id)
  };
  if (simulateResolve(payload) === c.key) p1++;
});

// TEST 2: Legacy payload ONLY (NO option_ids, only foreign keys)
let p2 = 0;
cases.forEach(c => {
  const [p, s, sk, sn] = c.key.split('|');
  const ops = [byKey.q1[p], byKey.q4[s || 'none'], byKey.skin_type[sk], byKey.sensitivity[sn === 'yes' ? 'yes' : 'no']];
  const payload = {
    skin_type_id: ops[2].mapped_id,
    routine_goal_id: ops[0].mapped_id,
    is_sensitive: ops[3].mapped_id,
    concern_ids: s !== 'none' ? [ops[1].mapped_id] : []
  };
  if (simulateResolve(payload) === c.key) p2++;
});

// TEST 3: Codes only (direct R479 contract)
let p3 = 0;
cases.forEach(c => {
  const [p, s, sk, sn] = c.key.split('|');
  const payload = {
    primary: p,
    secondary: s,
    skin: sk,
    sensitive: sn === 'yes'
  };
  if (simulateResolve(payload) === c.key) p3++;
});

console.log('TEST 1 (Full Payload with option_ids):', p1, '/ 728');
console.log('TEST 2 (Legacy Payload with foreign keys only):', p2, '/ 728');
console.log('TEST 3 (R479 Codes direct):', p3, '/ 728');
