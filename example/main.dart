import 'package:ovo/ovo.dart';

final m1 = ovo.map(ovo.string(), ovo.int());
final m2 = ovo.map(ovo.int(), ovo.string());

final schema = ovo.or([m1, m2]);

void main() async {
  final res1 = await schema.parse({'1': 1, '2': 2});
  print(res1);

  final res2 = await schema.parse({1: '1', 2: '2'});
  print(res2);

  await schema.parse(123); // Throws OvoException
}
